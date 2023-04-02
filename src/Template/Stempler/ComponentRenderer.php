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
                $componentArgs = '';

                foreach ($node->getAttributes() as $name => $value) {
                    $componentArgs .= \sprintf(', %s: %s', $name, $value);
                }

                $result->push(\sprintf(<<<'PHP'
<?php echo $this->container->get(\Spiral\Livewire\Livewire::class)->initialRequest('%s'%s); ?>
PHP
                , $node->name, $componentArgs), $node->getContext());
        }

        return true;
    }
}
