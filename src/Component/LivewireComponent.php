<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component;

use Psr\EventDispatcher\EventDispatcherInterface;
use Spiral\Core\ResolverInterface;
use Spiral\Livewire\Component\Trait\HandleActionTrait;
use Spiral\Livewire\Component\Trait\InteractsWithProperties;
use Spiral\Livewire\Component\Trait\ReceiveEvent;
use Spiral\Livewire\Component\Trait\TracksRenderedChildren;
use Spiral\Livewire\Event\Component\ComponentRendered;
use Spiral\Livewire\Event\Component\ComponentRendering;
use Spiral\Livewire\Event\Component\ViewRender;
use Spiral\Livewire\Exception\Component\BadMethodCallException;
use Spiral\Livewire\Exception\Component\RenderException;
use Spiral\Livewire\Request;
use Spiral\Livewire\Response;
use Spiral\Livewire\Str;
use Spiral\Router\RouterInterface;
use Spiral\Views\ViewInterface;
use Spiral\Views\ViewsInterface;

/**
 * @method void boot(...$params)
 * @method void mount(...$params)
 * @method void hydrate(Request $request)
 * @method void dehydrate(Response $response)
 * @method void updating(string $name, mixed $value)
 * @method void updated(string $name, mixed $value)
 */
abstract class LivewireComponent
{
    use HandleActionTrait;
    use InteractsWithProperties;
    use ReceiveEvent;
    use TracksRenderedChildren;

    protected ViewsInterface $views;
    protected ViewInterface $preRenderedView;
    protected array $renderContext = [];
    protected array $forStack = [];

    /**
     * @var non-empty-string
     */
    private string $name;

    /**
     * @var non-empty-string
     */
    private string $id;

    /**
     * @var non-empty-string
     */
    private string $template;

    private array $errors = [];
    private ResolverInterface $resolver;
    private EventDispatcherInterface $dispatcher;
    private PropertyHasherInterface $hasher;
    private RouterInterface $router;
    private bool $shouldSkipRender = false;
    private ?string $redirectTo = null;

    /**
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return non-empty-string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @throws \ReflectionException
     * @throws RenderException
     */
    public function renderToView(): ViewInterface
    {
        $this->dispatcher->dispatch(new ComponentRendering(component: $this));

        $view = method_exists($this, 'render')
            ? $this->render(...$this->resolver->resolveArguments(new \ReflectionMethod($this, 'render')))
            : $this->views->get($this->template);

        if (!$view instanceof ViewInterface) {
            throw new RenderException(sprintf('Method `render` must return instance of `%s`.', ViewInterface::class));
        }

        $this->dispatcher->dispatch(new ComponentRendered(component: $this, view: $view));

        return $this->preRenderedView = $view;
    }

    public function output(): ?string
    {
        if ($this->shouldSkipRender) {
            return null;
        }

        $view = $this->preRenderedView;
        $properties = $this->getPublicPropertiesDefinedBySubClass();

        $output = $view->render(array_merge($this->renderContext, $properties));

        $this->dispatcher->dispatch(new ViewRender(view: $view, output: $output));

        return $output;
    }

    public function setValidationErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    public function getForStack(): array
    {
        return $this->forStack;
    }

    /**
     * @param non-empty-string $url
     */
    public function redirectTo(string $url): void
    {
        $this->redirectTo = $url;

        $this->shouldSkipRender = true;
    }

    /**
     * @param non-empty-string $route
     */
    public function redirectToRoute(string $route, array $parameters = []): void
    {
        $this->redirectTo((string) $this->router->uri($route, $parameters));
    }

    /**
     * @return ?non-empty-string
     */
    public function getRedirectTo(): ?string
    {
        return $this->redirectTo;
    }

    /**
     * @throws BadMethodCallException
     */
    public function __call(string $method, mixed $params): void
    {
        if (
            \in_array($method, ['mount', 'hydrate', 'dehydrate', 'updating', 'updated'])
            || str_starts_with($method, 'updating')
            || str_starts_with($method, 'updated')
            || str_starts_with($method, 'hydrate')
            || str_starts_with($method, 'dehydrate')
        ) {
            // Eat calls to the lifecycle hooks if the dev didn't define them.
            return;
        }

        throw new BadMethodCallException(sprintf('Method %s::%s does not exist!', static::class, $method));
    }

    /**
     * @internal
     *
     * @param non-empty-string $name
     * @param non-empty-string $template
     */
    private function configure(
        string $name,
        string $template,
        ViewsInterface $views,
        ResolverInterface $resolver,
        PropertyHasherInterface $hasher,
        EventDispatcherInterface $dispatcher,
        RouterInterface $router
    ): void {
        $this->name = $name;
        $this->template = $template;
        $this->views = $views;
        $this->resolver = $resolver;
        $this->hasher = $hasher;
        $this->dispatcher = $dispatcher;
        $this->router = $router;

        $this->id = Str::random(20);
    }
}
