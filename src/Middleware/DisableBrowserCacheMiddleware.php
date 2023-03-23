<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Spiral\Livewire\Config\LivewireConfig;

final class DisableBrowserCacheMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly LivewireConfig $config
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if ($this->config->isBrowserCacheDisabled()) {
            $response = $response
                ->withAddedHeader('Pragma', 'no-cache')
                ->withAddedHeader('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT')
                ->withAddedHeader('Cache-Control', 'no-cache, must-revalidate, no-store, max-age=0, private');
        }

        return $response;
    }
}
