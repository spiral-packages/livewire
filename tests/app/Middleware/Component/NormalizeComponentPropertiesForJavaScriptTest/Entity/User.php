<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Middleware\Component\NormalizeComponentPropertiesForJavaScriptTest\Entity;

final class User
{
    public function __construct(
        public readonly int $id,
        public readonly string $name
    ) {
    }
}
