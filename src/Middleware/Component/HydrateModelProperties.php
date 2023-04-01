<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Spiral\Livewire\Component\DataAccessorInterface;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Exception\Component\PublicPropertyTypeNotAllowedException;
use Spiral\Livewire\Request;
use Spiral\Livewire\Response;

final class HydrateModelProperties implements HydrationMiddleware, DehydrationMiddleware, InitialDehydrationMiddleware
{
    public function __construct(
        private readonly DataAccessorInterface $dataAccessor
    ) {
    }

    public function hydrate(LivewireComponent $component, Request $request): void
    {
        $publicProperties = $request->memo['data'] ?? [];
        $dates = $request->memo['dataMeta']['dates'] ?? [];

        foreach ($publicProperties as $property => $value) {
            if ($type = $dates[$property] ?? null) {
                $types = [
                    'native' => \DateTime::class,
                    'nativeImmutable' => \DateTimeImmutable::class,
                ];
                if (class_exists(Carbon::class) && class_exists(CarbonImmutable::class)) {
                    $types['carbon'] = Carbon::class;
                    $types['carbonImmutable'] = CarbonImmutable::class;
                }

                $component->$property = new $types[$type]($value);
            } else {
                // If the value is null, don't set it, because all values start off as null and this
                // will prevent Typed properties from wining about being set to null.
                null === $value || $component->$property = $value;
            }
        }
    }

    /**
     * @throws PublicPropertyTypeNotAllowedException
     */
    public function initialDehydrate(LivewireComponent $component, Response $response): void
    {
        $this->dehydrateProperties($component, $response);
    }

    /**
     * @throws PublicPropertyTypeNotAllowedException
     */
    public function dehydrate(LivewireComponent $component, Response $response): void
    {
        $this->dehydrateProperties($component, $response);
    }

    /**
     * @throws PublicPropertyTypeNotAllowedException
     */
    private function dehydrateProperties(LivewireComponent $component, Response $response): void
    {
        $publicData = $this->dataAccessor->getData($component);

        $response->memo['data'] = [];
        $response->memo['dataMeta'] = [];

        array_walk($publicData, static function (mixed $value, string $key) use ($component, $response): void {
            if (
                // The value is a supported type, set it in the data, if not, throw an exception for the user.
                \is_bool($value) || \is_array($value) || is_numeric($value) || \is_string($value) || null === $value
            ) {
                $response->memo['data'][$key] = $value;
            } elseif ($value instanceof \DateTimeInterface) {
                $response->memo['dataMeta']['dates'][$key] = match (true) {
                    class_exists(Carbon::class) && $value instanceof Carbon => 'carbon',
                    class_exists(CarbonImmutable::class) && $value instanceof CarbonImmutable => 'carbonImmutable',
                    $value instanceof \DateTimeImmutable => 'nativeImmutable',
                    default => 'native'
                };

                $response->memo['data'][$key] = $value->format(\DateTimeInterface::ISO8601);
            } else {
                throw new PublicPropertyTypeNotAllowedException(
                    "Livewire component's `{$component->getName()}` public property `{$key}` must be of type: `numeric`, `string`, `array`, `null`, or `boolean`.\n".
                    "Only protected or private properties can be set as other types because JavaScript doesn't need to access them."
                );
            }
        });
    }
}
