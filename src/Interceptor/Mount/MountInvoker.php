<?php

declare(strict_types=1);

namespace Spiral\Livewire\Interceptor\Mount;

use Spiral\Core\CoreInterface;
use Spiral\Livewire\Component\LivewireComponent;

final class MountInvoker
{
    public function __construct(
        private readonly CoreInterface $core
    ) {
    }

    public function invoke(LivewireComponent $component, array $parameters): void
    {
        if (\method_exists($component, 'mount')) {
            $ref = new \ReflectionMethod($component, 'mount');

            if (\array_is_list($parameters)) {
                $params = [];
                foreach ($ref->getParameters() as $key => $parameter) {
                    if (isset($parameters[$key])) {
                        $params[$parameter->getName()] = $parameters[$key];
                    }
                }
                $parameters = $params;
            }

            $this->core->callAction($component::class, 'mount', [
                'component' => $component,
                'parameters' => $parameters,
                'reflection' => $ref
            ]);
        }
    }
}
