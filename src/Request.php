<?php

declare(strict_types=1);

namespace Spiral\Livewire;

/**
 * @psalm-type TFingerprint = array{
 *     id: non-empty-string,
 *     name: non-empty-string,
 *     locale?: string,
 *     path: string,
 *    method: string
 * }
 * @psalm-type TUpdates = array<array-key, mixed>
 * @psalm-type TMemo = array{
 *     errors: array<array-key, mixed>
 * }
 */
final class Request
{
    /** @var TFingerprint */
    public array $fingerprint;
    public array $updates;
    public array $memo;

    /**
     * @param array{
     *     fingerprint: TFingerprint,
     *     updates: TUpdates,
     *     serverMemo: TMemo
     * } $payload
     */
    public function __construct(array $payload)
    {
        $this->fingerprint = $payload['fingerprint'];
        $this->updates = $payload['updates'];
        $this->memo = $payload['serverMemo'];
    }

    /**
     * @return non-empty-string
     */
    public function getId(): string
    {
        return $this->fingerprint['id'];
    }

    /**
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->fingerprint['name'];
    }
}
