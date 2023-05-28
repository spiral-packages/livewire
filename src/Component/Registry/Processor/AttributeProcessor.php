<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component\Registry\Processor;

use Spiral\Attributes\ReaderInterface;
use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Component\Factory\FactoryInterface;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Component\Registry\ComponentRegistryInterface;
use Spiral\Tokenizer\TokenizationListenerInterface;
use Spiral\Tokenizer\TokenizerListenerRegistryInterface;

final class AttributeProcessor implements TokenizationListenerInterface, ProcessorInterface
{
    /**
     * @var array<non-empty-string, \ReflectionClass<LivewireComponent>>
     */
    private array $components = [];

    /**
     * @var array<non-empty-string, Component>
     */
    private array $attributes = [];

    private bool $collected = false;

    public function __construct(
        TokenizerListenerRegistryInterface $listenerRegistry,
        private readonly ReaderInterface $reader,
        private readonly FactoryInterface $factory,
        private readonly ComponentRegistryInterface $registry
    ) {
        $listenerRegistry->addListener($this);
    }

    public function process(): void
    {
        if (!$this->collected) {
            throw new \RuntimeException(sprintf('Tokenizer did not finalize %s listener.', self::class));
        }

        foreach ($this->components as $name => $ref) {
            $this->registry->add($name, $this->factory->create($name, $ref->getName(), $this->attributes[$name]));
        }
    }

    public function listen(\ReflectionClass $class): void
    {
        $attr = $this->reader->firstClassMetadata($class, Component::class);

        if ($attr instanceof Component) {
            /** @var \ReflectionClass<LivewireComponent> $componentRef */
            $componentRef = $class;

            $this->components[$attr->name ?? $class->getName()] = $componentRef;
            $this->attributes[$attr->name ?? $class->getName()] = $attr;
        }
    }

    public function finalize(): void
    {
        $this->collected = true;
    }
}
