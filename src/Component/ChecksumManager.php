<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component;

use Spiral\Encrypter\Config\EncrypterConfig;

final class ChecksumManager implements ChecksumManagerInterface
{
    public function __construct(
        private readonly EncrypterConfig $config
    ) {
    }

    public function generate(array $fingerprint, array $memo): string
    {
        $hashKey = $this->config->getKey();

        // It's actually Ok if the "children" tracking is tampered with.
        // Also, this way JavaScript can modify children as it needs to for
        // dom-diffing purposes.
        $memoSansChildren = array_diff_key($memo, array_flip(['children']));

        $stringForHashing = ''
            .json_encode($fingerprint)
            .json_encode($memoSansChildren);

        return hash_hmac('sha256', $stringForHashing, $hashKey);
    }

    public function check(string $checksum, array $fingerprint, array $memo): bool
    {
        return hash_equals($this->generate($fingerprint, $memo), $checksum);
    }
}
