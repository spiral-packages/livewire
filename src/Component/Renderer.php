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
        if ($component->shouldSkipRender()) {
            return null;
        }

        $view = $component->getPreRenderedView();

        /** @var non-empty-string $output */
        $output = $view->render(array_merge(
            $component->getRenderContext(),
            $this->dataAccessor->getData($component),
            ['errors' => $component->getValidationErrors()]
        ));

        $this->dispatcher->dispatch(new ViewRender(view: $view, output: $output));

        return $output;
    }
}
