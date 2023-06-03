<?php

declare(strict_types=1);

namespace Spiral\Livewire\Interceptor\Mount;

use Spiral\Core\CoreInterceptorInterface;
use Spiral\Core\CoreInterface;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Service\ArgumentTypecast;

/**
 * @psalm-type TParameters = array{
 *     component: LivewireComponent,
 *     reflection: \ReflectionMethod,
 *     parameters: array
 * }
 */
class TypecasterInterceptor implements CoreInterceptorInterface
{
    public function __construct(
        protected readonly ArgumentTypecast $argumentTypecast
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
        \assert($parameters['reflection'] instanceof \ReflectionMethod);
        \assert(\is_array($parameters['parameters']));

        $parameters['parameters'] = $this->argumentTypecast->cast($parameters['parameters'], $parameters['reflection']);

        return $core->callAction($controller, $action, $parameters);
    }
}
