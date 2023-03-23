# Livewire integration bridge for Spiral Framework

[![PHP Version Require](https://poser.pugx.org/spiral-packages/livewire/require/php)](https://packagist.org/packages/spiral-packages/livewire)
[![Latest Stable Version](https://poser.pugx.org/spiral-packages/livewire/v/stable)](https://packagist.org/packages/spiral-packages/livewire)
[![phpunit](https://github.com/spiral-packages/livewire/actions/workflows/phpunit.yml/badge.svg)](https://github.com/spiral-packages/livewire/actions)
[![psalm](https://github.com/spiral-packages/livewire/actions/workflows/psalm.yml/badge.svg)](https://github.com/spiral-packages/livewire/actions)
[![Codecov](https://codecov.io/gh/spiral-packages/livewire/branch/master/graph/badge.svg)](https://codecov.io/gh/spiral-packages/livewire)
[![Total Downloads](https://poser.pugx.org/spiral-packages/livewire/downloads)](https://packagist.org/packages/spiral-packages/livewire)
[![type-coverage](https://shepherd.dev/github/spiral-packages/livewire/coverage.svg)](https://shepherd.dev/github/spiral-packages/livewire)
[![psalm-level](https://shepherd.dev/github/spiral-packages/livewire/level.svg)](https://shepherd.dev/github/spiral-packages/livewire)

## WARNING!

The package is under active development. Don't use it in your applications.

## Requirements

Make sure that your server is configured with following PHP version and extensions:

- PHP 8.1+
- Spiral framework 3.6+

## Installation

You can install the package via composer:

```bash
composer require spiral-packages/livewire
```

To enable the package in your Spiral Framework application, you will need to add
the `Spiral\Livewire\Bootloader\LivewireBootloader` class to the list of bootloaders in your application:

```php
protected const LOAD = [
    // ...
    \Spiral\Livewire\Bootloader\LivewireBootloader::class,
];
```

> **Note**
> If you are using [`spiral-packages/discoverer`](https://github.com/spiral-packages/discoverer),
> you don't need to register bootloader by yourself.


### Template engines

> **Warning**
> The package currently **doesn`t support the Stempler** template engine.
> To use Livewire with the Spiral Framework, you will need to use the [**Twig**](https://github.com/spiral/twig-bridge) template engine.

To get started with **Livewire** and **Twig** in Spiral Framework application, need to add the
`Spiral\Livewire\Bootloader\TwigBootloader` class to the list of bootloaders in your application.

Here's an example of how to do that:

```php
    // ...
    \Spiral\Livewire\Bootloader\TwigBootloader::class,
```

When the **TwigBootloader** is registered, it provides the `Spiral\Livewire\Twig\Extension\LivewireExtension` extension
that allows to use the **livewire_styles** and **livewire_scripts** Twig functions and
`Spiral\Livewire\Twig\NodeVisitor\LivewireNodeVisitor`.
- **livewire_styles** and **livewire_scripts** - These functions are used to include the required Livewire CSS and JavaScript code.
- **LivewireNodeVisitor** - This node visitor is responsible for processing and transforming Livewire
  component tags, such as <livewire:counter foo="bar" />, into rendered HTML output with the component's initial state.

## Configuration

The configuration file should be located at **app/config/livewire.php**, and it allows you to set options.
Here is an example of how the configuration file might look:

```php
use Spiral\Events\Config\EventListener;
use Spiral\Livewire\Component\Registry\Processor\AttributeProcessor;
use Spiral\Livewire\Event\Component\ComponentHydrateSubsequent;
use Spiral\Livewire\Listener\Component\SupportChildren;
use Spiral\Livewire\Listener\Component\SupportLocales;
use Spiral\Livewire\Middleware\Component\CallHydrationHooks;
use Spiral\Livewire\Middleware\Component\CallPropertyHydrationHooks;
use Spiral\Livewire\Middleware\Component\HydratePublicProperties;

return [
    'listeners' => [
        // ...
        ComponentHydrateSubsequent::class => [
            new EventListener(
                listener: SupportLocales::class,
                method: 'onComponentHydrateSubsequent',
                priority: 10
            ),
            new EventListener(
                listener: SupportChildren::class,
                method: 'onComponentHydrateSubsequent',
                priority: 20
            ),
        ],
        // ...
    ],
    'initial_hydration_middleware' => [
        // ...
        CallHydrationHooks::class,
        // ...
    ],
    'hydration_middleware' => [
        // ...
        HydratePublicProperties::class,
        // ...
    ],
    'initial_dehydration_middleware' => [
        // ...
        CallPropertyHydrationHooks::class,
        // ...
    ],
    'dehydration_middleware' => [
        // ...
        CallPropertyHydrationHooks::class,
        // ...
    ],
    'processors' => [
        // ...
        AttributeProcessor::class,
        // ...
    ],
    'disable_browser_cache' => true,
];
```

> **Notice**
> The package is configured by default and does not require any additional configuration.
> Use the config file only if you need to change the default configuration.

> **Warning**
> The order of all middleware is important! The correct order can be viewed in the default configuration here:
> **src/Bootloader/ConfigBootloader.php**

## Usage

Add the **livewire_styles()** in the head section and **livewire_scripts()** before the closing body tag:

```html
<!DOCTYPE html>
<html lang="{{ locale }}">
    <head>
        // ...
        {{ livewire_styles() }}
    </head>
    <body>
        {% block body %}{% endblock %}
        {{ livewire_scripts() }}
    </body>
</html>
```

Lets create a simple Livewire component **Counter**:

```php
namespace App\Endpoint\Web\Livewire\Component;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Component\LivewireComponent;

#[Component(name: 'counter', template: 'components/counter.twig')]
final class Counter extends LivewireComponent
{
    public int $count = 0;

    public function increment(): void
    {
        $this->count++;
    }
}
```

Create a template:

```html
<div style="text-align: center">
    <button wire:click="increment">+</button>
    <h1>{{ count }}</h1>
</div>
```

Add **<livewire:counter />** anywhere in a Twig view and it will render.

```html
{% extends "layout/base.twig" %}

{% block body %}
    <livewire:counter />
{% endblock %}
```

Now reload the page in the browser, you should see the counter component rendered.
If you click the "+" button, the page should automatically update without a page reload.

## Testing

```bash
composer test
```

```bash
composer psalm
```

```bash
composer cs
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
