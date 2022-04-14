<?php

use GuzzleHttp\Psr7\Response;

function mockResponse($endpoint = 'stories', $headers = [], $version = 'v2', $statusCode = 200)
{
    $content = file_get_contents("./tests/Data/{$version}/{$endpoint}.json");

    return new Response($statusCode, $headers, $content);
}
