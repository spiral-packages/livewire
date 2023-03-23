<?php

declare(strict_types=1);

namespace Spiral\Livewire\Attribute;

use Spiral\Attributes\NamedArgumentConstructor;

#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class Component
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $template = null
    ) {
    }
}
