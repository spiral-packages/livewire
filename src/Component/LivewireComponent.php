<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component;

use Psr\EventDispatcher\EventDispatcherInterface;
use Spiral\Core\ResolverInterface;
use Spiral\Livewire\Component\Trait\ReceiveEvent;
use Spiral\Livewire\Component\Trait\TracksRenderedChildren;
use Spiral\Livewire\Event\Component\ComponentRendered;
use Spiral\Livewire\Event\Component\ComponentRendering;
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
    use ReceiveEvent;
    use TracksRenderedChildren;

    protected ViewsInterface $views;
    protected ViewInterface $preRenderedView;
    protected array $renderContext = [];
    protected array $validationContext = [];
    protected array $forStack = [];

    /**
     * @var non-empty-string
     */
    private string $livewireName;

    /**
     * @var non-empty-string
     */
    private string $livewireId;

    /**
     * @var non-empty-string
     */
    private string $livewireTemplate;

    private array $livewireErrors = [];
    private ResolverInterface $livewireResolver;
    private EventDispatcherInterface $livewireDispatcher;
    private PropertyHasherInterface $livewireHasher;
    private RouterInterface $livewireRouter;
    private bool $livewireShouldSkipRender = false;

    /**
     * @var ?non-empty-string
     */
    private ?string $livewireRedirectTo = null;

    /**
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->livewireName;
    }

    /**
     * @return non-empty-string
     */
    public function getId(): string
    {
        return $this->livewireId;
    }

    /**
     * @throws \ReflectionException
     * @throws RenderException
     */
    public function renderToView(): ViewInterface
    {
        $this->livewireDispatcher->dispatch(new ComponentRendering(component: $this));

        $view = method_exists($this, 'render')
            ? $this->render(...$this->livewireResolver->resolveArguments(new \ReflectionMethod($this, 'render')))
            : $this->views->get($this->livewireTemplate);

        if (!$view instanceof ViewInterface) {
            throw new RenderException(sprintf('Method `render` must return instance of `%s`.', ViewInterface::class));
        }

        $this->livewireDispatcher->dispatch(new ComponentRendered(component: $this, view: $view));

        return $this->preRenderedView = $view;
    }

    public function setValidationErrors(array $errors = []): void
    {
        $this->livewireErrors = $errors;
    }

    public function getValidationErrors(): array
    {
        return $this->livewireErrors;
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
        $this->livewireRedirectTo = $url;

        $this->livewireShouldSkipRender = true;
    }

    /**
     * @param non-empty-string $route
     */
    public function redirectToRoute(string $route, array $parameters = []): void
    {
        /** @var non-empty-string $uri */
        $uri = (string) $this->livewireRouter->uri($route, $parameters);

        $this->redirectTo($uri);
    }

    /**
     * @return ?non-empty-string
     */
    public function getRedirectTo(): ?string
    {
        return $this->livewireRedirectTo;
    }

    public function getValidationContext(): array
    {
        return $this->validationContext;
    }

    public function getPreRenderedView(): ViewInterface
    {
        return $this->preRenderedView;
    }

    public function getRenderContext(): array
    {
        return $this->renderContext;
    }

    public function shouldSkipRender(): bool
    {
        return $this->livewireShouldSkipRender;
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
        $this->livewireName = $name;
        $this->livewireTemplate = $template;
        $this->views = $views;
        $this->livewireResolver = $resolver;
        $this->livewireHasher = $hasher;
        $this->livewireDispatcher = $dispatcher;
        $this->livewireRouter = $router;

        $this->livewireId = Str::random(20);
    }
}
