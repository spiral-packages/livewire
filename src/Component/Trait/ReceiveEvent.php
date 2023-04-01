<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component\Trait;

use Spiral\Livewire\Component\Event;
use Spiral\Livewire\Event\Component\ActionReturned;
use Spiral\Livewire\Exception\Component\BadMethodCallException;

// TODO refactor or remove this trait
trait ReceiveEvent
{
    protected array $eventQueue = [];
    protected array $dispatchQueue = [];
    protected array $listeners = [];

    protected function getListeners(): array
    {
        return $this->listeners;
    }

    /**
     * @param non-empty-string $event
     */
    public function emit(string $event, mixed ...$params): Event
    {
        return $this->eventQueue[] = new Event($event, $params);
    }

    /**
     * @param non-empty-string $event
     */
    public function emitUp(string $event, mixed ...$params): void
    {
        $this->emit($event, ...$params)->up();
    }

    /**
     * @param non-empty-string $event
     */
    public function emitSelf(string $event, mixed ...$params): void
    {
        $this->emit($event, ...$params)->self();
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string $event
     */
    public function emitTo(string $name, string $event, mixed ...$params): void
    {
        $this->emit($event, ...$params)->component($name);
    }

    /**
     * @param non-empty-string $event
     */
    public function dispatchBrowserEvent(string $event, mixed $data = null): void
    {
        $this->dispatchQueue[] = [
            'event' => $event,
            'data' => $data,
        ];
    }

    public function getEventQueue(): array
    {
        return array_map(static fn (Event $event) => $event->serialize(), $this->eventQueue);
    }

    public function getDispatchQueue(): array
    {
        return $this->dispatchQueue;
    }

    protected function getEventsAndHandlers(): array
    {
        $handlers = [];
        foreach ($this->getListeners() as $key => $listener) {
            $handlers[is_numeric($key) ? $listener : $key] = $listener;
        }

        return $handlers;
    }

    public function getEventsBeingListenedFor(): array
    {
        return array_keys($this->getEventsAndHandlers());
    }

    /**
     * @param non-empty-string $event
     * @param non-empty-string $id
     *
     * @throws BadMethodCallException
     */
    public function fireEvent(string $event, mixed $params, string $id): void
    {
        $method = $this->getEventsAndHandlers()[$event];

        /** @psalm-suppress UndefinedMagicMethod */
        $this->callMethod($method, $params, function ($returned) use ($event, $id) {
            $this->livewireDispatcher->dispatch(new ActionReturned($this, $event, $returned, $id));
        });
    }
}
