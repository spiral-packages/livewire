<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component;

use Psr\EventDispatcher\EventDispatcherInterface;
use Spiral\Filters\Exception\ValidationException;
use Spiral\Livewire\Component\ActionHandlerInterface;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Event\Component\ActionReturned;
use Spiral\Livewire\Event\Component\FailedValidation;
use Spiral\Livewire\Exception\Component\BadMethodCallException;
use Spiral\Livewire\Exception\Component\DirectlyCallingLifecycleHooksNotAllowedException;
use Spiral\Livewire\Exception\Component\ModelNotWritableException;
use Spiral\Livewire\Request;

final class PerformActionCalls implements HydrationMiddleware
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly ActionHandlerInterface $actionHandler
    ) {
    }

    /**
     * @throws BadMethodCallException
     * @throws DirectlyCallingLifecycleHooksNotAllowedException
     * @throws ModelNotWritableException
     */
    public function hydrate(LivewireComponent $component, Request $request): void
    {
        try {
            foreach ($request->updates as $update) {
                if ('callMethod' !== $update['type']) {
                    continue;
                }

                $id = $update['payload']['id'];
                $method = $update['payload']['method'];
                $params = $update['payload']['params'];

                if (
                    \in_array($method, ['boot', 'mount', 'hydrate', 'dehydrate', 'updating', 'updated'])
                    || str_starts_with($method, 'updating')
                    || str_starts_with($method, 'updated')
                    || str_starts_with($method, 'hydrate')
                    || str_starts_with($method, 'dehydrate')
                ) {
                    throw new DirectlyCallingLifecycleHooksNotAllowedException(sprintf(
                        'Unable to call lifecycle method `%s` directly on component: `%s`.',
                        $method,
                        $component->getName()
                    ));
                }

                $this->actionHandler->callMethod(
                    $component,
                    $method,
                    $params,
                    function (mixed $returned) use ($component, $method, $id) {
                        $this->dispatcher->dispatch(new ActionReturned($component, $method, $returned, $id));
                    }
                );
            }
        } catch (ValidationException $e) {
            $this->dispatcher->dispatch(new FailedValidation($component, $e));

            $component->setValidationErrors($e->errors);
        }
    }
}
