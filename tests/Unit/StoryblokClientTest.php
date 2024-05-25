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

namespace SensioLabs\Storyblok\Api\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SensioLabs\Storyblok\Api\StoryblokClient;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class StoryblokClientTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function requestUrlMustStartWithTrailingSlash(): void
    {
        $client = self::createClient(new MockResponse());

        self::expectException(\InvalidArgumentException::class);

        $client->request('GET', self::faker()->word());
    }

    /**
     * @test
     */
    public function requestUrlMustNotStartWithHttp(): void
    {
        $client = self::createClient(new MockResponse());

        self::expectException(\InvalidArgumentException::class);

        $client->request('GET', self::faker()->url());
    }

    public static function createClient(ResponseInterface $response): StoryblokClient
    {
        return new StoryblokClient(
            baseUri: 'https://api.storyblok.com/',
            token: 'test-token',
            storyblokClient: new MockHttpClient($response, 'https://api.storyblok.com/'),
        );
    }
}
