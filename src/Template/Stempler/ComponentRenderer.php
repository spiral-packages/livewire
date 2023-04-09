<?php

declare(strict_types=1);

namespace Spiral\Livewire\Template\Stempler;

use Spiral\Livewire\Livewire;
use Spiral\Stempler\Compiler;
use Spiral\Stempler\Compiler\RendererInterface;
use Spiral\Stempler\Compiler\Result;
use Spiral\Stempler\Node\NodeInterface;

final class ComponentRenderer implements RendererInterface
{
    public function __construct(
        private readonly Livewire $livewire
    ) {
    }

    public function render(Compiler $compiler, Result $result, NodeInterface $node): bool
    {
        switch (true) {
            case $node instanceof Component:
                \assert(\is_string($node->name));
                $componentArgs = [];
                foreach ($node->getAttributes() as $name => $value) {
                    $componentArgs[$name] = \trim($value, '"');
                }
                $result->push($this->livewire->initialRequest($node->name, $componentArgs), $node->getContext());
        }

        return true;
    }
}
