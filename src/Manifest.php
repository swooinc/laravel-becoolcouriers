<?php

namespace SwooInc\BeCool;

use Illuminate\Support\Carbon;
use Illuminate\Support\Fluent;

class Manifest extends Fluent
{
    /**
     * Upload a manifest via the API.
     *
     * @param  string  $contents
     * @param  int  $region
     * @param  \Illuminate\Support\Carbon  $date
     * @param  int  $window
     * @param  int  $service
     * @param  bool  $sitesOnly
     * @param  \Illuminate\Support\Carbon|null  $overwrite
     * @return static
     */
    public static function upload(string $contents, int $region, Carbon $date, int $window, int $service, bool $sitesOnly = false, Carbon $overwrite = null): static
    {
        $client = resolve(Client::class);

        $response = $client
            ->attach('manifest', $contents, 'manifest.csv')
            ->post($client->getEndpoint('manifests/upload'), [
                'region' => $region,
                'date' => $date->format('Y-m-d'),
                'original_date' => optional($overwrite)->format('Y-m-d'),
                'default_time_window' => $window,
                'default_service' => $service,
                'sites_only' => $sitesOnly,
            ]);

        return new static([
            'id' => $response->json('job_id'),
        ]);
    }
}
