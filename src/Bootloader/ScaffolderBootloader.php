<?php

declare(strict_types=1);

namespace Spiral\Livewire\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Console\Bootloader\ConsoleBootloader;
use Spiral\Livewire\Scaffolder\Command\ComponentCommand;
use Spiral\Livewire\Scaffolder\Declaration\ComponentDeclaration;
use Spiral\Scaffolder\Bootloader\ScaffolderBootloader as Scaffolder;

final class ScaffolderBootloader extends Bootloader
{
    public function init(Scaffolder $scaffolder, ConsoleBootloader $console): void
    {
        $scaffolder->addDeclaration(ComponentDeclaration::TYPE, [
            'namespace' => 'Endpoint\\Livewire',
            'postfix' => 'Component',
            'class' => ComponentDeclaration::class,
        ]);
        $console->addCommand(ComponentCommand::class);
    }
}
