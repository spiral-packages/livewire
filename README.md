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

### Validation

The first step in enabling validation in your Livewire components is to make sure that the `spiral\validator`
package is installed and properly configured in your application.
Once you have ensured that the validator package is installed, you will need to add the
`Spiral\Livewire\Bootloader\ValidationBootloader` class to the list of bootloaders in your application:

```php
protected const LOAD = [
    // ...
    \Spiral\Livewire\Bootloader\ValidationBootloader::class,
];
```

After adding the ValidationBootloader class, you must implement the `Spiral\Filters\Model\ShouldBeValidated`
interface in your Livewire component and define the `validationRules` method to specify your validation rules.
This method should return an array of validation rules for each property that requires validation.

> **Notice**
> The validation rules are described in the [validator documentation](https://spiral.dev/docs/validation-spiral/3.6/en#validation-dsl).

For example, if you want to validate the **name** and **email** fields of a ContactForm component,
you could define the component like this:

```php
namespace App\Endpoint\Web\Livewire\Component;

use Spiral\Filters\Model\ShouldBeValidated;
use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Component\LivewireComponent;

#[Component(name: 'contact-form', template: 'components/contact-form.twig')]
final class ContactForm extends LivewireComponent implements ShouldBeValidated
{
    public string $name;
    public string $email;

    public function submit(): void
    {
        // This method will only be called if all the data is valid
    }

    public function validationRules(): array
    {
        return [
            'name' => ['required'],
            'email' => ['required', 'email']
        ];
    }
```

In this example, the validationRules method returns an array of rules that specify that both name and email are
required fields, and that the email field must be a valid email address.

- Validator can validate only single property when the property is updated. If the component is configured to update
  the data when the field changes. For example, a validator might validate an Email and display an error **before**
  the user clicks the Submit button.
- Before calling the component method, the validator will check all the data and only call the method if all data is valid.

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
