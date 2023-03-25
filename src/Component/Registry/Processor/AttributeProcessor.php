<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component\Registry\Processor;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Spiral\Attributes\ReaderInterface;
use Spiral\Core\ResolverInterface;
use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Component\PropertyHasherInterface;
use Spiral\Livewire\Component\Registry\ComponentRegistryInterface;
use Spiral\Livewire\Str;
use Spiral\Router\RouterInterface;
use Spiral\Tokenizer\TokenizationListenerInterface;
use Spiral\Tokenizer\TokenizerListenerRegistryInterface;
use Spiral\Views\ViewsInterface;

final class AttributeProcessor implements TokenizationListenerInterface, ProcessorInterface
{
    /**
     * @var array<non-empty-string, \ReflectionClass>
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
        private readonly ContainerInterface $container,
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
            $component = $this->container->get($ref->getName());

            \assert($component instanceof LivewireComponent);

            $ref->getMethod('configure')->invoke(
                $component,
                $name,
                $this->attributes[$name]->template ?? Str::kebab($ref->getName()),
                $this->container->get(ViewsInterface::class),
                $this->container->get(ResolverInterface::class),
                $this->container->get(PropertyHasherInterface::class),
                $this->container->get(EventDispatcherInterface::class),
                $this->container->get(RouterInterface::class)
            );

            $this->registry->add($component);
        }
    }

    public function listen(\ReflectionClass $class): void
    {
        $attr = $this->reader->firstClassMetadata($class, Component::class);

        if ($attr instanceof Component) {
            $this->components[$attr->name ?? $class->getName()] = $class;
            $this->attributes[$attr->name ?? $class->getName()] = $attr;
        }
    }

    public function finalize(): void
    {
        $this->collected = true;
    }
}
