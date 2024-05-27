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
use SensioLabs\Storyblok\Api\Response\DatasourceResponse;
use SensioLabs\Storyblok\Api\Response\DatasourcesResponse;
use Webmozart\Assert\Assert;

final readonly class DatasourcesApi implements DatasourcesApiInterface
{
    public function __construct(
        private StoryblokClientInterface $client,
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function all(?Pagination $pagination = null): DatasourcesResponse
    {
        $pagination ??= new Pagination(1, self::PER_PAGE);
        Assert::lessThanEq($pagination->perPage, self::MAX_PER_PAGE);

        try {
            $response = $this->client->request('GET', '/v2/cdn/datasources', [
                'query' => [
                    'page' => $pagination->page,
                    'per_page' => $pagination->perPage,
                ],
            ]);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }

        return new DatasourcesResponse(Total::fromHeaders($response->getHeaders()), $pagination, $response->toArray());
    }

    public function bySlug(string $datasourceSlug): DatasourceResponse
    {
        Assert::regex($datasourceSlug, '/^[a-z0-9-]+$/');

        try {
            $response = $this->client->request('GET', '/v2/cdn/datasources/'.$datasourceSlug);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }

        return new DatasourceResponse($response->toArray());
    }
}
