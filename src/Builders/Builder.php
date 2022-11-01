<?php

namespace SwooInc\BeCool\Builders;

use Illuminate\Support\Arr;

abstract class Builder
{
    /**
     * The options of the builder.
     *
     * @var array<string, mixed>
     */
    protected $options = [];

    /**
     * Alias of the constructor.
     *
     * @param  mixed  $args
     * @return static
     */
    public static function make(...$args): static
    {
        return new static(...$args);
    }

    /**
     * Update an option of the builder.
     *
     * @param  string  $name
     * @param  mixed  $key
     * @return static
     */
    public function withOption(string $name, $value): static
    {
        Arr::set($this->options, $name, $value);

        return $this;
    }
}
