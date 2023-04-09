<?php

declare(strict_types=1);

namespace Spiral\Livewire\Template\Stempler;

use Spiral\Stempler\Node\AttributedInterface;
use Spiral\Stempler\Node\NodeInterface;
use Spiral\Stempler\Node\Traits\AttributeTrait;
use Spiral\Stempler\Node\Traits\ContextTrait;
use Spiral\Stempler\Parser\Context;

/**
 * @implements NodeInterface<Component>
 * @template TNode of NodeInterface
 * @psalm-suppress MissingTemplateParam
 */
final class Component implements NodeInterface, AttributedInterface
{
    use AttributeTrait;
    use ContextTrait;

    /** @var list<TNode> */
    public array $nodes = [];

    /**
     * @param non-empty-string|null $name
     */
    public function __construct(
        public ?string $name,
        Context $context = null,
    ) {
        $this->context = $context;
    }

    public function getIterator(): \Generator
    {
        yield 'nodes' => $this->nodes;
    }
}
