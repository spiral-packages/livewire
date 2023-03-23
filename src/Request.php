<?php

declare(strict_types=1);

namespace Spiral\Livewire;

final class Request
{
    public array $fingerprint;
    public array $updates;
    public array $memo;

    /**
     * @param array{
     *     fingerprint: array{
     *         id: non-empty-string,
     *         name: non-empty-string,
     *         locale?: string,
     *         path: string,
     *         method: string
     *     },
     *     updates: array<array-key, mixed>,
     *     serverMemo: array{
     *         errors: array<array-key, mixed>
     *    }
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
