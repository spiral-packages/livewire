<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Template\Twig\NodeVisitor\LivewireNodeVisitorTest;

final class Modifier
{
    public function modify(int $value): int
    {
        return $value + 1;
    }
}
