<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component;

interface DataAccessorInterface
{
    public function getData(LivewireComponent $component): array;
}
