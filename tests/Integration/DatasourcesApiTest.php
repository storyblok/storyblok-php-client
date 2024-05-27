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
use SensioLabs\Storyblok\Api\DatasourcesApi;
use SensioLabs\Storyblok\Api\Domain\Value\Dto\Pagination;
use SensioLabs\Storyblok\Api\Response\DatasourceResponse;
use SensioLabs\Storyblok\Api\Response\DatasourcesResponse;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;
use SensioLabs\Storyblok\Api\Tests\Util\StoryblokFakeClient;

class DatasourcesApiTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function allDatasourcesAreRetrievedSuccessfully(): void
    {
        $client = StoryblokFakeClient::willRespond(
            self::faker()->datasourcesResponse(),
            ['total' => 10],
        );
        $api = new DatasourcesApi($client);

        $response = $api->all(new Pagination(1, 10));

        self::assertInstanceOf(DatasourcesResponse::class, $response);
    }

    /**
     * @test
     */
    public function datasourceIsRetrievedBySlugSuccessfully(): void
    {
        $client = StoryblokFakeClient::willRespond(
            ['datasource' => self::faker()->datasourceResponse()],
        );
        $api = new DatasourcesApi($client);

        $response = $api->bySlug('test-slug');

        self::assertInstanceOf(DatasourceResponse::class, $response);
    }

    /**
     * @test
     */
    public function exceptionIsThrownWhenRetrievingAllDatasourcesFails(): void
    {
        $client = StoryblokFakeClient::willThrowException(new \Exception());
        $api = new DatasourcesApi($client);

        self::expectException(\Exception::class);

        $api->all(new Pagination(1, 10));
    }

    /**
     * @test
     */
    public function exceptionIsThrownWhenRetrievingDatasourceBySlugFails(): void
    {
        $client = StoryblokFakeClient::willThrowException(new \Exception());
        $api = new DatasourcesApi($client);

        self::expectException(\Exception::class);

        $api->bySlug('test-slug');
    }
}
