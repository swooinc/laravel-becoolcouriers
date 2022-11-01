<?php

namespace SwooInc\BeCool\Http;

use Illuminate\Http\Client\Response as ClientResponse;

class Response
{
    /**
     * Create a new instance of the response.
     *
     * @param  \Illuminate\Http\Client  $response
     */
    public function __construct(protected ClientResponse $response)
    {
    }
}
