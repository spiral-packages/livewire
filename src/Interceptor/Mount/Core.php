<?php

declare(strict_types=1);

namespace Spiral\Livewire\Interceptor\Mount;

use Spiral\Core\CoreInterface;
use Spiral\Core\ResolverInterface;
use Spiral\Livewire\Component\LivewireComponent;

/**
 * @internal
 * @psalm-type TParameters = array{
 *     component: LivewireComponent,
 *     reflection: \ReflectionMethod,
 *     parameters: array
 * }
 */
final class Core implements CoreInterface
{
    public function __construct(
        private readonly ResolverInterface $resolver
    ) {
    }

    /**
     * @param-assert TParameters $parameters
     */
    public function callAction(string $controller, string $action, array $parameters = []): mixed
    {
        \assert($parameters['component'] instanceof LivewireComponent);
        \assert($parameters['reflection'] instanceof \ReflectionMethod);
        \assert(\is_array($parameters['parameters']));

        $parameters['component']->mount(
            ...$this->resolver->resolveArguments($parameters['reflection'], $parameters['parameters'])
        );

        return null;
    }
}
