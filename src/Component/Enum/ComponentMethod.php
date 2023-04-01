<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component\Enum;

enum ComponentMethod: string
{
    case Sync = '$sync';
    case Set = '$set';
    case Toggle = '$toggle';
    case Refresh = '$refresh';
}
