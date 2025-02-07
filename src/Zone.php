<?php

namespace SwooInc\BeCool;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use SwooInc\BeCool\Builders\ZoneBuilder;

class Zone extends Base
{
    /**
     * Create a new subscription instance.
     *
     * @param  iterable<TKey, TValue>  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        $locations = Arr::get($attributes, 'locations');

        if (is_array($locations)) {
            Arr::set(
                $attributes,
                'locations',
                Collection::make($locations)->mapInto(Location::class),
            );
        }

        $windows = Arr::get($attributes, 'time_windows');

        if (is_array($windows)) {
            Arr::set(
                $attributes,
                'time_windows',
                Collection::make($windows)->mapInto(TimeWindow::class)
            );
        }

        parent::__construct($attributes);
    }

    /**
     * Retrieve all the zones.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function all(): Collection
    {
        return ZoneBuilder::make()->get();
    }

    /**
     * Retrieve the time windows available to the zone.
     *
     * @param  bool  $all
     * @return \Illuminate\Support\Collection<int, \SwooInc\BeCool\TimeWindow>
     */
    public function getTimeWindows(bool $all = false): Collection
    {
        if ($this->time_windows === null) {
            $response = resolve(Client::class)->get(
                sprintf(
                    'zones/%d/timewindows',
                    $this->id
                ),
                [
                    'show_all' => $all,
                ]
            );

            $this->time_windows = $response->json();
        }

        return $this->time_windows;
    }

    /**
     * Set the value at the given offset.
     *
     * @param  TKey  $offset
     * @param  TValue  $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset == 'time_windows' && is_array($value)) {
            $value = collect($value)->mapInto(TimeWindow::class);
        }

        if ($offset == 'locations' && is_array($value)) {
            $value = collect($value)->mapInto(Location::class);
        }

        parent::offsetSet($offset, $value);
    }

    /**
     * Create a new query builder.
     *
     * @return \SwooInc\BeCool\Builders\ZoneBuilder
     */
    public static function query(): ZoneBuilder
    {
        return ZoneBuilder::make();
    }
}
