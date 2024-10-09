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

namespace SensioLabs\Storyblok\Api\Tests\Util;

use SensioLabs\Storyblok\Api\StoryblokClientInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Simon André <smn.andre@gmail.com>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class StoryblokFakeClient implements StoryblokClientInterface
{
    private function __construct(
        private HttpClientInterface $client,
    ) {
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        return $this->client->request($method, $url, $options);
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $headers
     */
    public static function willRespond(array $body, array $headers = []): self
    {
        return new self(new MockHttpClient(new JsonMockResponse($body, [
            'response_headers' => $headers,
        ])));
    }

    public static function willThrowException(\Throwable $e): self
    {
        return new self(new MockHttpClient(static fn () => throw $e));
    }
}
