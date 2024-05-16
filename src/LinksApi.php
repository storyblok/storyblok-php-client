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
use SensioLabs\Storyblok\Api\Domain\Value\Id;
use SensioLabs\Storyblok\Api\Domain\Value\Total;
use SensioLabs\Storyblok\Api\Response\LinksResponse;
use Webmozart\Assert\Assert;

final class LinksApi implements LinksApiInterface
{
    public function __construct(
        private StoryblokClientInterface $client,
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function all(?Pagination $pagination = null): LinksResponse
    {
        if (null === $pagination) {
            $pagination = new Pagination();
        }

        Assert::lessThanEq($pagination->perPage, self::MAX_PER_PAGE);

        try {
            $response = $this->client->request(
                'GET',
                '/v2/cdn/links',
                [
                    'query' => [
                        'paginated' => true,
                        'include_dates' => true,
                        'page' => $pagination->page,
                        'per_page' => $pagination->perPage,
                    ],
                ],
            );

            $this->logger->debug('Response', $response->toArray(false));

            return new LinksResponse(Total::fromHeaders($response->getHeaders()), $pagination, $response->toArray());
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }

    public function byParent(Id $parentId, ?Pagination $pagination = null): LinksResponse
    {
        if (null === $pagination) {
            $pagination = new Pagination();
        }

        Assert::lessThanEq($pagination->perPage, self::MAX_PER_PAGE);

        try {
            $response = $this->client->request(
                'GET',
                '/v2/cdn/links',
                [
                    'query' => [
                        'paginated' => true,
                        'include_dates' => true,
                        'with_parent' => $parentId->value,
                        'page' => $pagination->page,
                        'per_page' => $pagination->perPage,
                    ],
                ],
            );

            $this->logger->debug('Response', $response->toArray(false));

            return new LinksResponse(Total::fromHeaders($response->getHeaders()), $pagination, $response->toArray());
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }

    public function roots(?Pagination $pagination = null): LinksResponse
    {
        if (null === $pagination) {
            $pagination = new Pagination();
        }

        Assert::lessThanEq($pagination->perPage, self::MAX_PER_PAGE);

        try {
            $response = $this->client->request(
                'GET',
                '/v2/cdn/links',
                [
                    'query' => [
                        'paginated' => true,
                        'include_dates' => true,
                        'with_parent' => 0,
                        'page' => $pagination->page,
                        'per_page' => $pagination->perPage,
                    ],
                ],
            );

            $this->logger->debug('Response', $response->toArray(false));

            return new LinksResponse(Total::fromHeaders($response->getHeaders()), $pagination, $response->toArray());
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }
}
