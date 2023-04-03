<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component;

/**
 * @psalm-import-type TFingerprint from \Spiral\Livewire\Request
 * @psalm-import-type TMemo from \Spiral\Livewire\Request
 */
interface ChecksumManagerInterface
{
    /**
     * @param TFingerprint $fingerprint
     * @param TMemo $memo
     * @return non-empty-string
     */
    public function generate(array $fingerprint, array $memo): string;

    /**
     * @param non-empty-string $checksum
     * @param TFingerprint $fingerprint
     * @param TMemo $memo
     */
    public function check(string $checksum, array $fingerprint, array $memo): bool;
}
