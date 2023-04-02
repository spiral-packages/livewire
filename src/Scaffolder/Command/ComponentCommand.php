<?php

declare(strict_types=1);

namespace Spiral\Livewire\Scaffolder\Command;

use Spiral\Console\Attribute\Argument;
use Spiral\Console\Attribute\AsCommand;
use Spiral\Console\Attribute\Option;
use Spiral\Console\Attribute\Question;
use Spiral\Livewire\Scaffolder\Declaration\ComponentDeclaration;
use Spiral\Scaffolder\Command\AbstractCommand;

#[AsCommand(name: 'create:component', description: 'Create livewire component declaration')]
final class ComponentCommand extends AbstractCommand
{
    #[Argument(description: 'Component name')]
    #[Question(question: 'What would you like to name the component?')]
    public string $name;

    #[Option(shortcut: 'd', description: 'Command description')]
    public ?string $description = null;

    #[Option(shortcut: 'c', description: 'Optional comment to add as class header')]
    public ?string $comment = null;

    #[Option(description: 'Optional, specify a custom namespace')]
    public ?string $namespace = null;

    public function perform(): int
    {
        $declaration = $this->createDeclaration(ComponentDeclaration::class, [
            'alias' => \strtolower($this->name),
        ]);

        $this->writeDeclaration($declaration);

        return self::SUCCESS;
    }
}
