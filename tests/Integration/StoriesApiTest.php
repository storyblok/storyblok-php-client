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
use SensioLabs\Storyblok\Api\Domain\Value\Id;
use SensioLabs\Storyblok\Api\Domain\Value\Uuid;
use SensioLabs\Storyblok\Api\Response\StoriesResponse;
use SensioLabs\Storyblok\Api\Response\StoryResponse;
use SensioLabs\Storyblok\Api\StoriesApi;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;
use SensioLabs\Storyblok\Api\Tests\Util\StoryblokFakeClient;

final class StoriesApiTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function allStoriesAreRetrievedSuccessfully(): void
    {
        $client = StoryblokFakeClient::willRespond(
            self::faker()->storiesResponse(),
            ['total' => 1],
        );
        $api = new StoriesApi($client);

        $response = $api->all();

        self::assertInstanceOf(StoriesResponse::class, $response);
        self::assertSame(1, $response->total->value);
    }

    /**
     * @test
     */
    public function allStoriesByContentTypeAreRetrievedSuccessfully(): void
    {
        $client = StoryblokFakeClient::willRespond(
            self::faker()->storiesResponse(),
            ['total' => 1],
        );
        $api = new StoriesApi($client);

        $response = $api->allByContentType(self::faker()->word());

        self::assertInstanceOf(StoriesResponse::class, $response);
        self::assertSame(1, $response->total->value);
    }

    /**
     * @test
     */
    public function storyBySlugIsRetrievedSuccessfully(): void
    {
        $client = StoryblokFakeClient::willRespond(
            self::faker()->storyResponse(),
        );
        $api = new StoriesApi($client);

        $response = $api->bySlug('test-slug');

        self::assertInstanceOf(StoryResponse::class, $response);
    }

    /**
     * @test
     */
    public function storyByUuidIsRetrievedSuccessfully(): void
    {
        $client = StoryblokFakeClient::willRespond(
            self::faker()->storyResponse(),
        );
        $api = new StoriesApi($client);

        $response = $api->byUuid(new Uuid(self::faker()->uuid()));

        self::assertInstanceOf(StoryResponse::class, $response);
    }

    /**
     * @test
     */
    public function storyByIdIsRetrievedSuccessfully(): void
    {
        $client = StoryblokFakeClient::willRespond(
            self::faker()->storyResponse(),
        );
        $api = new StoriesApi($client);

        $response = $api->byId(new Id(14));

        self::assertInstanceOf(StoryResponse::class, $response);
    }

    /**
     * @test
     */
    public function exceptionIsThrownWhenRetrievingAllStoriesFails(): void
    {
        $client = StoryblokFakeClient::willThrowException(new \Exception());
        $api = new StoriesApi($client);

        self::expectException(\Exception::class);

        $api->all();
    }

    /**
     * @test
     */
    public function exceptionIsThrownWhenRetrievingAllStoriesByContentTypeFails(): void
    {
        $client = StoryblokFakeClient::willThrowException(new \Exception());
        $api = new StoriesApi($client);

        self::expectException(\Exception::class);

        $api->allByContentType(self::faker()->word());
    }

    /**
     * @test
     */
    public function exceptionIsThrownWhenRetrievingAllStoriesByIdFails(): void
    {
        $client = StoryblokFakeClient::willThrowException(new \Exception());
        $api = new StoriesApi($client);

        self::expectException(\Exception::class);

        $api->byId(new Id(self::faker()->numberBetween(1)));
    }

    /**
     * @test
     */
    public function exceptionIsThrownWhenRetrievingAllStoriesByUuidFails(): void
    {
        $client = StoryblokFakeClient::willThrowException(new \Exception());
        $api = new StoriesApi($client);

        self::expectException(\Exception::class);

        $api->byUuid(new Uuid(self::faker()->uuid()));
    }

    /**
     * @test
     */
    public function exceptionIsThrownWhenRetrievingAllStoriesBySlugFails(): void
    {
        $client = StoryblokFakeClient::willThrowException(new \Exception());
        $api = new StoriesApi($client);

        self::expectException(\Exception::class);

        $api->bySlug(self::faker()->slug());
    }
}
