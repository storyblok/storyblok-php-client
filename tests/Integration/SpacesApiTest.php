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

namespace SensioLabs\Storyblok\Api\Tests\Integration;

use PHPUnit\Framework\TestCase;
use SensioLabs\Storyblok\Api\Domain\Value\Space;
use SensioLabs\Storyblok\Api\Response\SpaceResponse;
use SensioLabs\Storyblok\Api\SpacesApi;
use SensioLabs\Storyblok\Api\StoryblokClient;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;

final class SpacesApiTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function me(): void
    {
        $client = self::createClient([
            'space' => self::faker()->spaceResponse([
                'id' => 123,
                'name' => 'foo',
                'domain' => 'bar',
                'version' => 1,
                'language_codes' => ['de', 'en'],
            ]),
        ]);
        $api = new SpacesApi($client);

        $response = $api->me();

        self::assertInstanceOf(SpaceResponse::class, $response);
        self::assertInstanceOf(Space::class, $space = $response->space);

        self::assertSame(123, $space->id->value);
        self::assertSame('foo', $space->name);
        self::assertSame('bar', $space->domain);
        self::assertSame(1, $space->version);
        self::assertSame(['de', 'en'], $space->languageCodes);
    }

    /**
     * @test
     */
    public function meThrowsExceptionIfSpaceIsMissing(): void
    {
        $client = self::createClient([]);
        $api = new SpacesApi($client);

        self::expectException(\InvalidArgumentException::class);

        $api->me();
    }

    /**
     * @param array<string, mixed> $response
     */
    public static function createClient(array $response): StoryblokClient
    {
        $client = new StoryblokClient(
            baseUri: 'https://example.com/',
            token: 'token',
        );

        $client->withHttpClient(new MockHttpClient(
            new JsonMockResponse($response),
            'https://api.storyblok.com/',
        ));

        return $client;
    }
}
