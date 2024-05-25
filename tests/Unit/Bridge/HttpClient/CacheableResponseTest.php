<?php

declare(strict_types=1);

/**
 * This file is part of Storyblok-Api.
 *
 * (c) SensioLabs Deutschland <info@sensiolabs.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SensioLabs\Storyblok\Api\Tests\Unit\Bridge\HttpClient;

use PHPUnit\Framework\TestCase;
use SensioLabs\Storyblok\Api\Bridge\HttpClient\CacheableResponse;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use function Safe\json_decode;

class CacheableResponseTest extends TestCase
{
    /**
     * @test
     */
    public function getStatusCode(): void
    {
        $client = new MockHttpClient(new MockResponse(info: ['http_code' => 201]));
        $response = $client->request('GET', 'https://example.com/');

        self::assertSame(201, (new CacheableResponse($response))->getStatusCode());
    }

    /**
     * @test
     */
    public function getHeaders(): void
    {
        $client = new MockHttpClient(
            new MockResponse(info: ['response_headers' => $headers = ['accept' => [0 => 'application/json']]]),
        );
        $response = $client->request('GET', 'https://example.com/');

        self::assertSame($headers, (new CacheableResponse($response))->getHeaders());
    }

    /**
     * @test
     */
    public function getContent(): void
    {
        $client = new MockHttpClient(new MockResponse($body = 'response body'));
        $response = $client->request('GET', 'https://example.com/');

        self::assertSame($body, (new CacheableResponse($response))->getContent());
    }

    /**
     * @test
     */
    public function toArray(): void
    {
        $json = '[{"hello there": "General Kenobi"}]';

        $client = new MockHttpClient(new MockResponse($json));
        $response = $client->request('GET', 'https://example.com/');

        self::assertSame(json_decode($json, true), (new CacheableResponse($response))->toArray());
    }

    /**
     * @test
     */
    public function toArrayWithInvalidJsonThrowsException(): void
    {
        $client = new MockHttpClient(
            new MockResponse('[{"hello there "General Kenobi"}]'),
        );
        $response = $client->request('GET', 'https://example.com/');

        self::expectException(\RuntimeException::class);

        (new CacheableResponse($response))->toArray();
    }

    /**
     * @test
     */
    public function cancelThrowsException(): void
    {
        $client = new MockHttpClient(new MockResponse());
        $response = $client->request('GET', 'https://example.com/');

        self::expectException(\BadMethodCallException::class);

        (new CacheableResponse($response))->cancel();
    }

    /**
     * @test
     */
    public function getInfoThrowsException(): void
    {
        $client = new MockHttpClient(new MockResponse());
        $response = $client->request('GET', 'https://example.com/');

        self::expectException(\BadMethodCallException::class);

        (new CacheableResponse($response))->getInfo();
    }
}
