<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\LivewireTest;

final class Service
{
    public function modify(string $value): string
    {
        return $value . ' modified by service';
    }

    public function modifyInt(int $value): int
    {
        return $value + 1;
    }
}
