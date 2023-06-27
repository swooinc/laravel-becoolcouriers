<?php

namespace SwooInc\BeCool\Builders;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use SwooInc\BeCool\Client;
use SwooInc\BeCool\Exceptions\ZoneNotFoundException;
use SwooInc\BeCool\Zone;
use Throwable;

class ZoneBuilder extends Builder
{
    /**
     * Alias of the get method.
     *
     * @return \Illuminate\Support\Collection<int, \SwooInc\BeCool\Zone>
     */
    public function all(): Collection
    {
        return $this->get();
    }

    /**
     * Retrieve the zones that matches the given query.
     *
     * @param  string  $query
     * @return \Illuminate\Support\Collection<int, \SwooInc\BeCool\Zone>
     */
    public function autocomplete(string $query): Collection
    {
        try {
            $response = resolve(Client::class)->get('zones/autocomplete', [
                's' => $query,
            ]);

            return collect($response->json())
                ->unique('zone')
                ->map(function ($suggestion) {
                    return $this->find(Arr::get($suggestion, 'zone'));
                });
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }
    }

    /**
     * Retrieve a zone by it's id.
     *
     * @param  int  $id
     * @return \SwooInc\BeCool\Zone|null
     */
    public function find(int $id): ?Zone
    {
        try {
            $response = resolve(Client::class)
                ->get("zones/{$id}", $this->options);

            return new Zone($response->json());
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }
    }

    /**
     * Retrieve the first zone from the response.
     *
     * @return \SwooInc\BeCool\Zone|null
     */
    public function first(): ?Zone
    {
        return $this->get()->first();
    }

    /**
     * Filter the zones that contains the given postcode.
     *
     * @param  string  $postcode
     * @return static
     */
    public function forPostcode(string $postcode): static
    {
        return $this->withOption('postcode', $postcode);
    }

    /**
     * Retrieve the list of zones.
     *
     * @return \Illuminate\Support\Collection<int, \SwooInc\BeCool\Zone>
     *
     * @throws \SwooInc\BeCool\Exceptions\ZoneNotFoundException
     */
    public function get(): Collection
    {
        $key = $this->getCacheKey();

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $postcode = Arr::get($this->options, 'postcode');

        try {
            $response = resolve(Client::class)->get('zones', $this->options);

            $data = $response->json();
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
                postcode: $postcode
            );
        }

        if ($postcode) {
            $zones = collect([
                new Zone($data),
            ]);
        } else {
            $zones = collect($data)->mapInto(Zone::class);
        }

        if ($duration = config('becool.cache.zones')) {
            Cache::put($key, $zones, now()->addSeconds($duration));
        }

        return $zones;
    }

    /**
     * Retrieve the key for caching the response.
     *
     * @return string
     */
    protected function getCacheKey(): string
    {
        return sprintf(
            'becool-zones-cache-%s',
            md5(json_encode($this->options))
        );
    }

    /**
     * Throw a new friendly exception based on the existing exception.
     *
     * @param  \Throwable  $exception
     * @param  string|null  $postcode
     * @return void
     */
    protected function throw(Throwable $exception, string $postcode = null): void
    {
        if ($exception->getCode() == 404 && $postcode) {
            throw new ZoneNotFoundException(
                __('Zone for :postcode not found.', [
                    'postcode' => $postcode,
                ]),
                $exception->getCode(),
                $exception
            );
        }

        throw $exception;
    }

    /**
     * Indicates that the request should include the locations.
     *
     * @return static
     */
    public function withLocations(): static
    {
        return $this->withOption('locations', true);
    }

    /**
     * Indicates that the request should include the time windows.
     *
     * @return static
     */
    public function withTimeWindows(): static
    {
        return $this->withOption('timewindow', true);
    }
}
