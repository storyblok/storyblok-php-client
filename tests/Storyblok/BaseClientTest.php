<?php

use Storyblok\BaseClient;
use Storyblok\Client;

test('set api key', function () {
    $client = new BaseClient('test');
    $this->assertEquals('test', $client->getApiKey());
    $client->setApiKey('new-test');
    $this->assertEquals('new-test', $client->getApiKey());
});

test('set proxy', function () {
    $client = new BaseClient('test');
    $client->setProxy('http://127.0.0.1');

    $this->assertEquals('http://127.0.0.1', $client->getProxy());
});

test('set timeout', function () {
    $client = new BaseClient('test');
    $client->setTimeout(20);

    $this->assertEquals(20, $client->getTimeout());
});

test('set max retries', function () {
    $client = new BaseClient('test');
    $client->setMaxRetries(3);

    $this->assertEquals(3, $client->getMaxRetries());
});

test('get headers', function () {
    $client = new Client('test', $endpoint = 'stories', $version = 'v1');
    $client->mockable([
        mockResponse($endpoint, ['server' => 'nginx/1.18.0'], $version),
    ]);

    $headers = $client->getStories()->getHeaders();

    $this->assertEquals('nginx/1.18.0', $headers['server'][0]);
});

test('get body', function () {
    $client = new Client('test', $endpoint = 'stories', $version = 'v1');
    $client->mockable([
        mockResponse($endpoint, [], $version),
    ]);

    $body = $client->getStories()->getBody();

    $this->assertArrayHasKey('stories', $body);
});

test('get status code', function () {
    $client = new Client('test', $endpoint = 'stories', $version = 'v1');
    $client->mockable([
        mockResponse($endpoint, [], $version, 202),
    ]);

    $statusCode = $client->getStories()->getCode();

    $this->assertEquals(202, $statusCode);
});
