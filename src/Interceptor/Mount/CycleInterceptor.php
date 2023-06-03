<?php

declare(strict_types=1);

namespace Spiral\Livewire\Interceptor\Mount;

use Cycle\ORM\ORMInterface;
use Spiral\Core\CoreInterceptorInterface;
use Spiral\Core\CoreInterface;
use Spiral\Core\Exception\ControllerException;
use Spiral\Livewire\Component\LivewireComponent;

/**
 * Automatically resolves cycle entities based on given parameter.
 *
 * @psalm-type TParameters = array{
 *     component: LivewireComponent,
 *     reflection: \ReflectionMethod,
 *     parameters: array
 * }
 */
class CycleInterceptor implements CoreInterceptorInterface
{
    /**
     * [class:method][parameter] = resolved role
     * @var array<non-empty-string, array<non-empty-string, non-empty-string>>
     */
    protected array $cache = [];

    public function __construct(
        protected readonly ORMInterface $orm
    ) {
    }

    /**
     * @param-assert TParameters $parameters
     *
     * @param class-string<LivewireComponent> $controller
     * @param non-empty-string $action
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function process(string $controller, string $action, array $parameters, CoreInterface $core): mixed
    {
        $this->cache = [];

        $entities = $this->getDeclaredEntities($controller, $action);

        $contextCandidates = [];
        foreach ($entities as $parameter => $role) {
            $value = $this->getParameter($parameter, $parameters);
            if ($value === null) {
                throw new ControllerException(
                    "Entity `{$parameter}` can not be found.",
                    ControllerException::NOT_FOUND
                );
            }

            if (\is_object($value)) {
                if ($this->orm->getHeap()->has($value)) {
                    $contextCandidates[] = $value;
                }

                // pre-filled
                continue;
            }

            $entity = $this->resolveEntity($role, $value);
            if ($entity === null) {
                throw new ControllerException(
                    "Entity `{$parameter}` can not be found.",
                    ControllerException::NOT_FOUND
                );
            }

            $parameters['parameters'][$parameter] = $entity;
            $contextCandidates[] = $entity;
        }

        if (!isset($parameters['parameters']['@context']) && \count($contextCandidates) === 1) {
            $parameters['parameters']['@context'] = \current($contextCandidates);
        }

        return $core->callAction($controller, $action, $parameters);
    }

    protected function getParameter(string $role, array $parameters): mixed
    {
        return $parameters['parameters'][$role] ?? null;
    }

    /**
     * @param non-empty-string $role
     */
    protected function resolveEntity(string $role, mixed $parameter): ?object
    {
        return $this->orm->getRepository($role)->findByPK($parameter);
    }

    /**
     * @param class-string<LivewireComponent> $controller
     * @param non-empty-string $action
     *
     * @return array<non-empty-string, non-empty-string>
     */
    protected function getDeclaredEntities(string $controller, string $action): array
    {
        /** @var non-empty-string $key */
        $key = \sprintf('%s:%s', $controller, $action);
        if (\array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        $this->cache[$key] = [];
        try {
            $method = new \ReflectionMethod($controller, $action);
        } catch (\ReflectionException) {
            return [];
        }

        foreach ($method->getParameters() as $parameter) {
            $class = $this->getParameterClass($parameter);

            if ($class === null) {
                continue;
            }

            if ($this->orm->getSchema()->defines($class->getName())) {
                /** @var non-empty-string $role */
                $role = $this->orm->resolveRole($class->getName());
                $this->cache[$key][$parameter->getName()] = $role;
            }
        }

        return $this->cache[$key];
    }

    protected function getParameterClass(\ReflectionParameter $parameter): ?\ReflectionClass
    {
        $type = $parameter->getType();

        if (!$type instanceof \ReflectionNamedType) {
            return null;
        }

        if ($type->isBuiltin()) {
            return null;
        }

        return new \ReflectionClass($type->getName());
    }
}
