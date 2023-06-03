<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Unit\Interceptor\Mount;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\RepositoryInterface;
use Cycle\ORM\SchemaInterface;
use PHPUnit\Framework\TestCase;
use Spiral\Core\CoreInterface;
use Spiral\Core\Exception\ControllerException;
use Spiral\Livewire\Interceptor\Mount\CycleInterceptor;
use Spiral\Livewire\Tests\App\Interceptor\Mount\CycleInterceptorTest\Component\Component;
use Spiral\Livewire\Tests\App\Interceptor\Mount\CycleInterceptorTest\Entity\User;

final class CycleInterceptorTest extends TestCase
{
    public function testProcess(): void
    {
        $entity = new User(1, 'foo');

        $repository = $this->createMock(RepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('findByPK')
            ->with(1)
            ->willReturn($entity);

        $schema = $this->createMock(SchemaInterface::class);
        $schema
            ->expects($this->once())
            ->method('defines')
            ->with(User::class)
            ->willReturn(true);

        $orm = $this->createMock(ORMInterface::class);
        $orm
            ->expects($this->once())
            ->method('getSchema')
            ->willReturn($schema);
        $orm
            ->expects($this->once())
            ->method('resolveRole')
            ->with(User::class)
            ->willReturn('user');
        $orm
            ->expects($this->once())
            ->method('getRepository')
            ->with('user')
            ->willReturn($repository);

        $core = $this->createMock(CoreInterface::class);
        $core
            ->expects($this->once())
            ->method('callAction')
            ->with(
                $this->callback(fn (mixed $component) => Component::class === $component),
                $this->callback(fn (mixed $action) => 'mount' === $action),
                $this->callback(fn (mixed $parameters) => $entity === $parameters['parameters']['user'])
            );

        $interceptor = new CycleInterceptor($orm);

        $interceptor->process(
            Component::class,
            'mount',
            ['parameters' => ['user' => 1]],
            $core
        );
    }

    public function testProcessWithValueNull(): void
    {
        $schema = $this->createMock(SchemaInterface::class);
        $schema
            ->expects($this->once())
            ->method('defines')
            ->with(User::class)
            ->willReturn(true);

        $orm = $this->createMock(ORMInterface::class);
        $orm
            ->expects($this->once())
            ->method('getSchema')
            ->willReturn($schema);
        $orm
            ->expects($this->once())
            ->method('resolveRole')
            ->with(User::class)
            ->willReturn('user');

        $interceptor = new CycleInterceptor($orm);

        $this->expectException(ControllerException::class);
        $interceptor->process(
            Component::class,
            'mount',
            ['parameters' => []],
            $this->createMock(CoreInterface::class)
        );
    }

    public function testProcessEntityNotFound(): void
    {
        $repository = $this->createMock(RepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('findByPK')
            ->with(1)
            ->willReturn(null);

        $schema = $this->createMock(SchemaInterface::class);
        $schema
            ->expects($this->once())
            ->method('defines')
            ->with(User::class)
            ->willReturn(true);

        $orm = $this->createMock(ORMInterface::class);
        $orm
            ->expects($this->once())
            ->method('getSchema')
            ->willReturn($schema);
        $orm
            ->expects($this->once())
            ->method('resolveRole')
            ->with(User::class)
            ->willReturn('user');
        $orm
            ->expects($this->once())
            ->method('getRepository')
            ->with('user')
            ->willReturn($repository);

        $interceptor = new CycleInterceptor($orm);

        $this->expectException(ControllerException::class);
        $interceptor->process(
            Component::class,
            'mount',
            ['parameters' => ['user' => 1]],
            $this->createMock(CoreInterface::class)
        );
    }
}
