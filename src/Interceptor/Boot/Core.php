<?php

declare(strict_types=1);

namespace Spiral\Livewire\Interceptor\Boot;

use Spiral\Core\CoreInterface;
use Spiral\Core\ResolverInterface;
use Spiral\Livewire\Component\LivewireComponent;

/**
 * @internal
 * @psalm-type TParameters = array{
 *     component: LivewireComponent,
 *     reflection: \ReflectionMethod
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

        $parameters['component']->boot(...$this->resolver->resolveArguments($parameters['reflection']));

        return null;
    }
}
