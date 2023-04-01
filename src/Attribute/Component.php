<?php

declare(strict_types=1);

namespace Spiral\Livewire\Attribute;

use Spiral\Attributes\NamedArgumentConstructor;

#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class Component
{
    /**
     * @param ?non-empty-string $name
     * @param ?non-empty-string $template
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $template = null
    ) {
    }
}
