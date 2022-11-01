<?php

namespace SwooInc\BeCool\Builders;

use Illuminate\Support\Collection;
use SwooInc\BeCool\Client;
use SwooInc\BeCool\Zone;

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
     * Retrieve the list of zones.
     *
     * @return \Illuminate\Support\Collection<int, \SwooInc\BeCool\Zone>
     */
    public function get(): Collection
    {
        $response = resolve(Client::class)->get('zones', $this->options);

        return collect($response->json())->mapInto(Zone::class);
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
