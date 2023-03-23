<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component\Trait;

trait TracksRenderedChildren
{
    protected array $renderedChildren = [];
    protected array $previouslyRenderedChildren = [];

    public function getRenderedChildComponentId(string $id): string
    {
        return $this->previouslyRenderedChildren[$id]['id'];
    }

    public function getRenderedChildComponentTagName(string $id): string
    {
        return $this->previouslyRenderedChildren[$id]['tag'];
    }

    public function logRenderedChild(string $id, string $componentId, string $tagName): void
    {
        $this->renderedChildren[$id] = ['id' => $componentId, 'tag' => $tagName];
    }

    public function preserveRenderedChild(string $id): void
    {
        $this->renderedChildren[$id] = $this->previouslyRenderedChildren[$id];
    }

    public function childHasBeenRendered(string $id): bool
    {
        return \array_key_exists($id, $this->previouslyRenderedChildren);
    }

    public function setPreviouslyRenderedChildren(mixed $children): void
    {
        $this->previouslyRenderedChildren = $children;
    }

    public function getRenderedChildren(): array
    {
        return $this->renderedChildren;
    }

    public function keepRenderedChildren(): void
    {
        $this->renderedChildren = $this->previouslyRenderedChildren;
    }
}
