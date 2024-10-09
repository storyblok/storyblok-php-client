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
use SensioLabs\Storyblok\Api\Response\TagsResponse;
use SensioLabs\Storyblok\Api\TagsApi;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;
use SensioLabs\Storyblok\Api\Tests\Util\StoryblokFakeClient;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Simon André <smn.andre@gmail.com>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
class TagsApiTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function allTagsAreRetrievedSuccessfully(): void
    {
        $client = StoryblokFakeClient::willRespond(
            ['tags' => []],
        );
        $api = new TagsApi($client);

        $response = $api->all();

        self::assertInstanceOf(TagsResponse::class, $response);
    }

    /**
     * @test
     */
    public function exceptionIsThrownWhenRetrievingAllTagsFails(): void
    {
        $client = StoryblokFakeClient::willThrowException(new \Exception());
        $api = new TagsApi($client);

        self::expectException(\Exception::class);

        $api->all();
    }
}
