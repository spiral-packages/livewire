<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component;

final class Event
{
    private string $name;
    private mixed $params;
    private bool $up;
    private bool $self;
    private ?string $component = null;

    public function __construct(string $name, mixed $params)
    {
        $this->name = $name;
        $this->params = $params;
    }

    public function up(): self
    {
        $this->up = true;

        return $this;
    }

    public function self(): self
    {
        $this->self = true;

        return $this;
    }

    public function component(string $name): self
    {
        $this->component = $name;

        return $this;
    }

    public function serialize(): array
    {
        $output = [
            'event' => $this->name,
            'params' => $this->params,
        ];

        if ($this->up) {
            $output['ancestorsOnly'] = true;
        }
        if ($this->self) {
            $output['selfOnly'] = true;
        }
        if (null !== $this->component) {
            $output['to'] = $this->component;
        }

        return $output;
    }
}
