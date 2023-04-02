<?php

declare(strict_types=1);

namespace Spiral\Livewire\Template\Stempler;

use Spiral\Stempler\Node\HTML\Tag;
use Spiral\Stempler\VisitorContext;
use Spiral\Stempler\VisitorInterface;

final class NodeVisitor implements VisitorInterface
{
    public function enterNode(mixed $node, VisitorContext $ctx): mixed
    {
        if ($node instanceof Tag) {
            if (\str_starts_with($node->name, 'livewire:')) {
                $c = new Component(\substr($node->name, 9), $node->getContext());
                foreach ($node->attrs as $attribute) {
                    if (\is_string($attribute->value)) {
                        $c->setAttribute($attribute->name, $attribute->value);
                    }
                }

                return $c;
            }
        }

        return $node;
    }

    public function leaveNode(mixed $node, VisitorContext $ctx): mixed
    {
        return null;
    }
}