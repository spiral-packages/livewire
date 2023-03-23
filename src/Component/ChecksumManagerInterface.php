<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component;

interface ChecksumManagerInterface
{
    public function generate(array $fingerprint, array $memo): string;

    public function check(string $checksum, array $fingerprint, array $memo): bool;
}
