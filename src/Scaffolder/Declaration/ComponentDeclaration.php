<?php

declare(strict_types=1);

namespace Spiral\Livewire\Scaffolder\Declaration;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Scaffolder\Config\ScaffolderConfig;
use Spiral\Scaffolder\Declaration\AbstractDeclaration;

final class ComponentDeclaration extends AbstractDeclaration
{
    public const TYPE = 'component';

    public function __construct(
        private readonly string $alias,
        ScaffolderConfig $config,
        string $name,
        ?string $comment = null,
        ?string $namespace = null,
    ) {
        parent::__construct($config, $name, $comment, $namespace);
    }

    public function declare(): void
    {
        $extends = LivewireComponent::class;

        $this->namespace->addUse($extends);
        $this->namespace->addUse(Model::class);
        $this->namespace->addUse(Component::class);
        $this->class->setExtends($extends);

        $this->class->setFinal();
        $this->class->addAttribute(Component::class, [
            'name' => $this->alias,
            'template' => 'components/' . $this->alias,
        ]);
    }
}
