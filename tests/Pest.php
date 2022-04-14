<?php

use GuzzleHttp\Psr7\Response;
use Storyblok\Client;

function mockResponse($endpoint = 'stories', $headers = [], $version = 'v2', $statusCode = 200)
{
    $content = file_get_contents("./tests/Data/{$version}/{$endpoint}.json");

    return new Response($statusCode, $headers, $content);
}

function mockClient($endpoint, $token, $version = 'v2')
{
    $mocks = [
        new Response(200, ['server' => 'nginx/1.18.0'], ),
    ];

    return (new Client($token, null, $version))
        ->mockable($mocks, $version)
    ;
}
