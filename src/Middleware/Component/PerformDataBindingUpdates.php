<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component;

use Psr\EventDispatcher\EventDispatcherInterface;
use Spiral\Filters\Exception\ValidationException;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Event\Component\FailedValidation;
use Spiral\Livewire\Request;

final class PerformDataBindingUpdates implements HydrationMiddleware
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function hydrate(LivewireComponent $component, Request $request): void
    {
        try {
            foreach ($request->updates as $update) {
                if ('syncInput' !== $update['type']) {
                    continue;
                }

                $data = $update['payload'];

                if (!\array_key_exists('value', $data)) {
                    continue;
                }

                $component->syncInput($data['name'], $data['value']);
            }
        } catch (ValidationException $e) {
            $this->dispatcher->dispatch(new FailedValidation($component, $e));

            $component->setValidationErrors($e->errors);
        }
    }
}
