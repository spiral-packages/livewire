<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component;

use Psr\EventDispatcher\EventDispatcherInterface;
use Spiral\Filters\Exception\ValidationException;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Event\Component\FailedValidation;
use Spiral\Livewire\Exception\Component\BadMethodCallException;
use Spiral\Livewire\Request;

final class PerformEventEmissions implements HydrationMiddleware
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    /**
     * @throws BadMethodCallException
     */
    public function hydrate(LivewireComponent $component, Request $request): void
    {
        try {
            foreach ($request->updates as $update) {
                if ('fireEvent' !== $update['type']) {
                    continue;
                }

                $component->fireEvent(
                    $update['payload']['event'],
                    $update['payload']['params'],
                    $update['payload']['id']
                );
            }
        } catch (ValidationException $e) {
            $this->dispatcher->dispatch(new FailedValidation($component, $e));

            $component->setValidationErrors($e->errors);
        }
    }
}
