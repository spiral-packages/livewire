<?php

declare(strict_types=1);

namespace Spiral\Livewire\Listener\Component;

use Psr\Container\ContainerInterface;
use Spiral\Livewire\Event\Component\ComponentCallingMethod;
use Spiral\Livewire\Event\Component\ComponentDehydrate;
use Spiral\Livewire\Event\Component\ComponentUpdating;
use Spiral\Livewire\Exception\Validation\ValidationException;
use Spiral\Livewire\Validation\ValidatorInterface;

final class SupportValidation
{
    public function __construct(
        private readonly ContainerInterface $container
    ) {
    }

    public function onComponentDehydrate(ComponentDehydrate $event): void
    {
        $event->response->memo['errors'] = $event->component->toArray()['errors'];
    }

    /**
     * Validate all properties before calling method.
     */
    public function onComponentCallingMethod(ComponentCallingMethod $event): void
    {
        if (!$validator = $this->getValidator()) {
            return;
        }

        try {
            $validator->validate($event->component);
            $event->component->setValidationErrors();
        } catch (ValidationException $exception) {
            $event->shouldSkipCalling = true;
            $event->component->setValidationErrors($exception->getErrors());
        }
    }

    /**
     * Validate single property on every update.
     */
    public function onComponentUpdating(ComponentUpdating $event): void
    {
        if (!$validator = $this->getValidator()) {
            return;
        }

        $errors = $event->component->toArray()['errors'];

        try {
            $validator->validateProperty($event->name, $event->value, $event->component);
            unset($errors[$event->name]);
        } catch (ValidationException $exception) {
            $errors[$event->name] = $exception->getErrors()[$event->name];
        } finally {
            $event->component->setValidationErrors($errors);
        }
    }

    private function getValidator(): ?ValidatorInterface
    {
        if (!$this->container->has(ValidatorInterface::class)) {
            return null;
        }

        return $this->container->get(ValidatorInterface::class);
    }
}
