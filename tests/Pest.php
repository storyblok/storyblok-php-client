<?php

use GuzzleHttp\Psr7\Response;
use Storyblok\Client;

function mockClient($endpoint, $token, $version = 'v2')
{
    $mocks = [
        new Response(200, [], file_get_contents("./tests/Data/$version/$endpoint.json"))
    ];

    return (new Client($token, null, $version))
        ->mockable($mocks, $version);
}
