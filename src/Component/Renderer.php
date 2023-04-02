<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component;

use Psr\EventDispatcher\EventDispatcherInterface;
use Spiral\Livewire\Event\Component\ViewRender;

final class Renderer implements RendererInterface
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly DataAccessorInterface $dataAccessor
    ) {
    }

    /**
     * @return ?non-empty-string
     */
    public function render(LivewireComponent $component): ?string
    {
        $data = $component->toArray();

        if ($data['shouldSkipRender']) {
            return null;
        }

        $view = $data['preRenderedView'];

        /** @var non-empty-string $output */
        $output = $view->render(array_merge(
            $data['renderContext'],
            $this->dataAccessor->getData($component),
            ['errors' => $data['errors']]
        ));

        $this->dispatcher->dispatch(new ViewRender(view: $view, output: $output));

        return $output;
    }
}
