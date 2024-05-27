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

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SensioLabs\Storyblok\Api\Domain\Value\Dto\Pagination;
use SensioLabs\Storyblok\Api\Domain\Value\Total;
use SensioLabs\Storyblok\Api\Response\DatasourceEntriesResponse;
use Webmozart\Assert\Assert;

final readonly class DatasourceEntriesApi implements DatasourceEntriesApiInterface
{
    public function __construct(
        private StoryblokClientInterface $client,
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function all(?Pagination $pagination = null): DatasourceEntriesResponse
    {
        return $this->collectionRequest([], $pagination);
    }

    public function allByDatasource(string $datasource, ?Pagination $pagination = null): DatasourceEntriesResponse
    {
        Assert::regex($datasource, '/^[a-z0-9-]+$/');

        return $this->collectionRequest(['datasource' => $datasource], $pagination);
    }

    public function allByDimension(string $dimension, ?Pagination $pagination = null): DatasourceEntriesResponse
    {
        Assert::regex($dimension, '/^[a-z0-9-]+$/');

        return $this->collectionRequest(['datasource' => $dimension], $pagination);
    }

    public function allByDatasourceDimension(string $datasource, string $dimension, ?Pagination $pagination = null): DatasourceEntriesResponse
    {
        Assert::regex($datasource, '/^[a-z0-9-]+$/');
        Assert::regex($dimension, '/^[a-z0-9-]+$/');

        return $this->collectionRequest(['datasource' => $datasource, 'dimension' => $dimension], $pagination);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function collectionRequest(array $parameters, ?Pagination $pagination = null): DatasourceEntriesResponse
    {
        $pagination ??= new Pagination(1, self::PER_PAGE);
        Assert::lessThanEq($pagination->perPage, self::MAX_PER_PAGE);

        try {
            $response = $this->client->request('GET', '/v2/cdn/datasource_entries', [
                'query' => [
                    ...$parameters,
                    'page' => $pagination->page,
                    'per_page' => $pagination->perPage,
                ],
            ]);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }

        return new DatasourceEntriesResponse(Total::fromHeaders($response->getHeaders()), $pagination, $response->toArray());
    }
}
