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

namespace Integration;

use PHPUnit\Framework\TestCase;
use SensioLabs\Storyblok\Api\Domain\Value\Id;
use SensioLabs\Storyblok\Api\LinksApi;
use SensioLabs\Storyblok\Api\Response\LinksResponse;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;
use SensioLabs\Storyblok\Api\Tests\Util\StoryblokFakeClient;

final class LinksApiTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function all(): void
    {
        $client = StoryblokFakeClient::willRespond(
            self::faker()->linksResponse(),
            ['total' => 1],
        );
        $api = new LinksApi($client);

        $response = $api->all();

        self::assertInstanceOf(LinksResponse::class, $response);
        self::assertSame(1, $response->total->value);
    }

    /**
     * @test
     */
    public function byParent(): void
    {
        $client = StoryblokFakeClient::willRespond(
            self::faker()->linksResponse(),
            ['total' => 1],
        );
        $api = new LinksApi($client);

        $response = $api->byParent(new Id(3));

        self::assertInstanceOf(LinksResponse::class, $response);
        self::assertSame(1, $response->total->value);
    }

    /**
     * @test
     */
    public function roots(): void
    {
        $client = StoryblokFakeClient::willRespond(
            self::faker()->linksResponse(),
            ['total' => 5],
        );
        $api = new LinksApi($client);

        $response = $api->roots();

        self::assertInstanceOf(LinksResponse::class, $response);
        self::assertSame(5, $response->total->value);
    }
}
