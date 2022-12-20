<?php

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Storyblok\ApiException;
use Storyblok\ManagementClient;

test('can be instanced', function () {
    $this->assertInstanceOf(ManagementClient::class, new ManagementClient('token'));
});

test('response has body', function () {
    $client = new ManagementClient('token');
    $client->mockable([
        new Response(200, [], '{"foo":"bar"}'),
    ]);

    $response = $client->post('test', []);

    $this->assertEquals(['foo' => 'bar'], $response->getBody());
});

test('response has header', function () {
    $client = new ManagementClient('token');
    $client->mockable([
        new Response(200, ['foo' => 'bar'], ''),
    ]);

    $response = $client->post('test', []);

    $this->assertEquals(['foo' => ['bar']], $response->getHeaders());
});

test('response has status code', function () {
    $client = new ManagementClient('token');
    $client->mockable([
        new Response(200, [], ''),
    ]);

    $response = $client->post('test', []);

    $this->assertEquals(200, $response->getCode());
});

test('response has proxy', function () {
    $client = new ManagementClient('token');
    $client->setProxy('127.0.0.1');
    $client->mockable([
        new Response(201, [], ''),
        new Response(200, [], ''),
        new Response(200, [], ''),
    ]);

    $post = $client->post('test', []);
    $put = $client->put('test', []);
    $delete = $client->delete('test', []);

    $this->assertEquals('127.0.0.1', $client->getProxy());
    $this->assertEquals(201, $post->httpResponseCode);
    $this->assertEquals(200, $put->httpResponseCode);
});

test('post request throws exception on error', function () {
    $client = new ManagementClient('token');
    $client->mockable([
        new ClientException('Error', new Request('POST', 'test'), new Response()),
    ]);

    $this->expectException(ApiException::class);

    $client->post('test', []);
});

test('put request throws exception on error', function () {
    $client = new ManagementClient('token');
    $client->mockable([
        new ClientException('Error', new Request('PUT', 'test'), new Response()),
    ]);

    $this->expectException(ApiException::class);

    $client->put('test', []);
});

test('delete request throws exception on error', function () {
    $client = new ManagementClient('token');
    $client->mockable([
        new ClientException('Error', new Request('DELETE', 'test'), new Response()),
    ]);

    $this->expectException(ApiException::class);

    $client->delete('test', []);
});
