<?php

namespace SwooInc\BeCool;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Client
{
    /**
     * The base endpoint of the API.
     *
     * @var string
     */
    protected $base = 'https://api.becoolcouriers.com.au/';

    /**
     * Create a new instance of the client.
     *
     * @param  string  $key
     * @param  bool  $sandbox
     */
    public function __construct(protected string $key, bool $sandbox = false)
    {
        if ($sandbox) {
            $this->base = Str::replaceFirst('api', 'stage-api', $this->base);
        }
    }

    /**
     * Send a GET request to the given API endpoint.
     *
     * @param  string  $endpoint
     * @param  array  $query
     * @return \Illuminate\Http\Client\Response
     */
    public function get(string $endpoint, array $query = []): Response
    {
        return $this->send('get', $endpoint, $query);
    }

    /**
     * Retrieve the full endpoint of the request.
     *
     * @return string
     */
    public function getEndpoint(string $path): string
    {
        return "{$this->base}{$path}";
    }

    /**
     * Create a new pending request.
     *
     * @return \Illuminate\Http\Client\PendingRequest
     */
    public function newRequest(): PendingRequest
    {
        return Http::throw()->withHeaders([
            'X-Api-Key' => $this->key,
        ]);
    }

    /**
     * Send a request to the given API endpoint.
     *
     * @param  string  $method
     * @param  string  $endpoint
     * @param  array  $payload
     * @return \Illuminate\Http\Client\Response
     */
    public function send(string $method, string $endpoint, array $payload = []): Response
    {
        $endpoint = $this->getEndpoint($endpoint);

        return $this->newRequest()->{$method}($endpoint, $payload);
    }
}
