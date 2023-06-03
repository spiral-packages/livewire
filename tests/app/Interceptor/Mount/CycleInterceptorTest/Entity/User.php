<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Interceptor\Mount\CycleInterceptorTest\Entity;

final class User
{
    public function __construct(
        public readonly int $id,
        public readonly string $name
    ) {
    }
}
