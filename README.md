# Livewire integration bridge for Spiral Framework

[![PHP Version Require](https://poser.pugx.org/spiral-packages/livewire/require/php)](https://packagist.org/packages/spiral-packages/livewire)
[![Latest Stable Version](https://poser.pugx.org/spiral-packages/livewire/v/stable)](https://packagist.org/packages/spiral-packages/livewire)
[![phpunit](https://github.com/spiral-packages/livewire/actions/workflows/phpunit.yml/badge.svg)](https://github.com/spiral-packages/livewire/actions)
[![psalm](https://github.com/spiral-packages/livewire/actions/workflows/psalm.yml/badge.svg)](https://github.com/spiral-packages/livewire/actions)
[![Codecov](https://codecov.io/gh/spiral-packages/livewire/branch/1.x/graph/badge.svg)](https://codecov.io/gh/spiral-packages/livewire)
[![Total Downloads](https://poser.pugx.org/spiral-packages/livewire/downloads)](https://packagist.org/packages/spiral-packages/livewire)
[![type-coverage](https://shepherd.dev/github/spiral-packages/livewire/coverage.svg)](https://shepherd.dev/github/spiral-packages/livewire)
[![psalm-level](https://shepherd.dev/github/spiral-packages/livewire/level.svg)](https://shepherd.dev/github/spiral-packages/livewire)


## WARNING!
This package is currently under active development. It is not recommended for use in production environments due to potential instability.

## Requirements

Make sure that your server is configured with following PHP version and extensions:

- PHP 8.1+
- Spiral framework 3.7+

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

#### Twig

To get started with **Livewire** and **Twig** in Spiral Framework application, need to add the
`Spiral\Livewire\Bootloader\TwigBootloader` class to the list of bootloaders in your application.

Here's an example of how to do that:

```php
    // ...
    \Spiral\Livewire\Bootloader\TwigBootloader::class,
```

When the **TwigBootloader** is registered, it provides the `Spiral\Livewire\Twig\Extension\LivewireExtension` extension
that allows to use the **livewire_styles**, **livewire_scripts**, **livewire** Twig functions.
- **livewire_styles** and **livewire_scripts** - These functions are used to include the required Livewire CSS and JavaScript code.
- **livewire** - This function takes the `name` of the component as the first parameter and renders the
  initial state of the component. Subsequent parameters will be passed to the component's **mount** method.

#### Stempler

To get started with **Livewire** and **Stempler** in Spiral Framework application, need to add the
`Spiral\Livewire\Bootloader\StemplerBootloader` class to the list of bootloaders in your application.

Here's an example of how to do that:

```php
    // ...
    \Spiral\Livewire\Bootloader\StemplerBootloader::class,
```

When the **StemplerBootloader** is registered, it provides the `Spiral\Livewire\Template\Stempler\LivewireDirective`
directive that allows to use the **livewireStyles**, **livewireScripts** directives and
`Spiral\Livewire\Template\Stempler\NodeVisitor` that can render a Livewire component on a page using the
**<livewire:name />** tag syntax.
- **livewireStyles** and **livewireScripts** - These functions are used to include the required Livewire CSS and JavaScript code.

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
use Spiral\Livewire\Middleware\Component\HydrateModelProperties;
use Spiral\Livewire\Interceptor\Mount\CycleInterceptor;
use Spiral\Livewire\Interceptor\Mount\TypecasterInterceptor;

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
    'interceptors' => [
        'mount' => [
            CycleInterceptor::class,
            TypecasterInterceptor::class,
        ],
        'boot' => []
    ],
    'initial_hydration_middleware' => [
        // ...
        CallHydrationHooks::class,
        // ...
    ],
    'hydration_middleware' => [
        // ...
        HydrateModelProperties::class,
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

Add the required Livewire CSS and JavaScript code:

### Twig

```html
<!DOCTYPE html>
<html lang="@{locale}">
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

### Stempler

```html
<!DOCTYPE html>
<html lang="@{locale}">
<head>
    // ...
    @livewireStyles
</head>
<body>
    // ...
    @livewireScripts
</body>
</html>
```

Lets create a simple Livewire component **Counter**:

```php
namespace App\Endpoint\Web\Livewire\Component;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;

#[Component(name: 'counter', template: 'components/counter')]
final class Counter extends LivewireComponent
{
    #[Model]
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

Call the Livewire component anywhere in your template.

### Twig

```html
{{ livewire('counter') }}
```

### Stempler

```html
<livewire:counter />
```

Now reload the page in the browser, you should see the counter component rendered.
If you click the "+" button, the page should automatically update without a page reload.

### Validation

The first step in enabling validation in your Livewire components is to make sure that `spiral\validator` or
`spiral-packages/laravel-validator` packages are installed and properly configured in your application.
Once you have ensured that the validator package is installed, you will need to add the
`Spiral\Livewire\Bootloader\ValidationBootloader` class to the list of bootloaders in your application:

```php
protected const LOAD = [
    // ...
    \Spiral\Livewire\Bootloader\ValidationBootloader::class,
];
```

After adding the ValidationBootloader class, you must implement the `Spiral\Livewire\Validation\ShouldBeValidated`
interface in your Livewire component and define the `validationRules` method to specify your validation rules.
This method should return an array of validation rules for each property that requires validation.
**Validation rules must be in the format supported by the validator you are using.**

> **Notice**
> Validation rules are described in the [Spiral Validator](https://spiral.dev/docs/validation-spiral/3.6/en#validation-dsl),
> [Laravel Validator](https://laravel.com/docs/10.x/validation#available-validation-rules) documentation.

For example, if you want to validate the **name** and **email** fields of a ContactForm component,
you could define the component like this.

#### Spiral Validator

```php
namespace App\Endpoint\Web\Livewire\Component;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Validation\ShouldBeValidated;

#[Component(name: 'contact-form', template: 'components/contact-form.twig')]
final class ContactForm extends LivewireComponent implements ShouldBeValidated
{
    #[Model]
    public string $name;

    #[Model]
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

#### Laravel Validator

```php
namespace App\Endpoint\Web\Livewire\Component;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Validation\ShouldBeValidated;

#[Component(name: 'contact-form', template: 'components/contact-form.twig')]
final class ContactForm extends LivewireComponent implements ShouldBeValidated
{
    #[Model]
    public string $name;

    #[Model]
    public string $email;

    public function submit(): void
    {
        // This method will only be called if all the data is valid
    }

    public function validationRules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|email'
        ];
    }
```

In this examples, the validationRules method returns an array of rules that specify that both name and email are
required fields, and that the email field must be a valid email address.

- Validator can validate only single property when the property is updated. If the component is configured to update
  the data when the field changes. For example, a validator might validate an Email and display an error **before**
  the user clicks the Submit button.
- Before calling the component method, the validator will check all the data and only call the method if all data is valid.

### Interceptors

Spiral provides a way for developers to customize the behavior of their executing `boot` and `mount` methods through
interceptors. An interceptor is a piece of code that is executed before or after a mount or boot method is called.

Some interceptors are provided by the package and enabled by default.
- **Spiral\Livewire\Interceptor\Mount\CycleInterceptor** - This is `mount` interceptor.
  Automatically resolves Cycle entities based on given parameter.
- **Spiral\Livewire\Interceptor\Mount\TypecasterInterceptor** - This is `mount` interceptor.
  Automatically converts the parameters passed to the mount method to the required type
  (**bool**, **int**, **float**, **array**).

> **Notice**
> The `CycleInterceptor` requires [Cycle ORM Bridge](https://github.com/spiral/cycle-bridge).
> If you don't use it, the interceptor will not be activated.

You can create an interceptor yourself and register it in the config file.

```php
namespace App\Interceptor;

use Spiral\Core\CoreInterceptorInterface;
use Spiral\Core\CoreInterface;
use Spiral\Livewire\Component\LivewireComponent;

class SomeInterceptor implements CoreInterceptorInterface
{
    /**
     * @param class-string $controller Component class name
     * @param string $action method (boot or mount)
     * @param array{
     *     component: LivewireComponent,
     *     reflection: \ReflectionMethod,
     *     parameters: array
     * } $parameters Array with additional parameters.
     *  For the mount method, contains an array with the parameters that were passed to it.
     */
    public function process(string $controller, string $action, array $parameters, CoreInterface $core): mixed
    {
        // Some code before calling method mount or boot

        $core->callAction($controller, $action, $parameters);

        // Some code after calling method mount or boot

        return null;
    }
}
```

```php
// file app/config/livewire.php
use App\Interceptor\SomeInterceptor;
use Spiral\Livewire\Interceptor\Mount\CycleInterceptor;
use Spiral\Livewire\Interceptor\Mount\TypecasterInterceptor;

return [
    'interceptors' => [
        'mount' => [
            SomeInterceptor::class,
            CycleInterceptor::class,
            TypecasterInterceptor::class,
        ],
        'boot' => [
            SomeInterceptor::class,
        ]
    ],
];
```

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
