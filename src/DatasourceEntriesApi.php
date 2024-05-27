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

namespace SensioLabs\Storyblok\Api;

use SensioLabs\Storyblok\Api\Domain\Value\Total;
use SensioLabs\Storyblok\Api\Request\DatasourceEntriesRequest;
use SensioLabs\Storyblok\Api\Response\DatasourceEntriesResponse;
use Webmozart\Assert\Assert;

final readonly class DatasourceEntriesApi implements DatasourceEntriesApiInterface
{
    private const string ENDPOINT = '/v2/cdn/datasource_entries';
    public function __construct(
        private StoryblokClientInterface $client,
    ) {
    }

    public function all(?DatasourceEntriesRequest $request = null): DatasourceEntriesResponse
    {
        $request ??= new DatasourceEntriesRequest();

        $response = $this->client->request('GET', self::ENDPOINT, [
            'query' => $request->toArray(),
        ]);

        return new DatasourceEntriesResponse(
            Total::fromHeaders($response->getHeaders()),
            $request->pagination,
            $response->toArray(),
        );
    }

    public function allByDatasource(string $datasource, ?DatasourceEntriesRequest $request = null): DatasourceEntriesResponse
    {
        Assert::regex($datasource, '/^[a-z0-9-]+$/');

        $request ??= new DatasourceEntriesRequest();

        $response = $this->client->request('GET', self::ENDPOINT, [
            'query' => [
                ...$request->toArray(),
                'datasource' => $datasource,
            ],
        ]);

        return new DatasourceEntriesResponse(
            Total::fromHeaders($response->getHeaders()),
            $request->pagination,
            $response->toArray(),
        );
    }

    public function allByDimension(string $dimension, ?DatasourceEntriesRequest $request = null): DatasourceEntriesResponse
    {
        Assert::regex($dimension, '/^[a-z0-9-]+$/');

        $request ??= new DatasourceEntriesRequest();

        $response = $this->client->request('GET', self::ENDPOINT, [
            'query' => [
                ...$request->toArray(),
                'dimension' => $dimension,
            ],
        ]);

        return new DatasourceEntriesResponse(
            Total::fromHeaders($response->getHeaders()),
            $request->pagination,
            $response->toArray(),
        );
    }

    public function allByDatasourceDimension(string $datasource, string $dimension, ?DatasourceEntriesRequest $request = null): DatasourceEntriesResponse
    {
        Assert::regex($datasource, '/^[a-z0-9-]+$/');
        Assert::regex($dimension, '/^[a-z0-9-]+$/');

        $request ??= new DatasourceEntriesRequest();

        $response = $this->client->request('GET', self::ENDPOINT, [
            'query' => [
                ...$request->toArray(),
                'datasource' => $datasource,
                'dimension' => $dimension,
            ],
        ]);

        return new DatasourceEntriesResponse(
            Total::fromHeaders($response->getHeaders()),
            $request->pagination,
            $response->toArray(),
        );
    }
}
