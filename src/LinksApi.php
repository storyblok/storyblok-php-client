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
use SensioLabs\Storyblok\Api\Domain\Value\Id;
use SensioLabs\Storyblok\Api\Response\LinksResponse;

final class LinksApi implements LinksApiInterface
{
    public function __construct(
        private StoryblokClientInterface $client,
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function all(array $parameter = []): LinksResponse
    {
        try {
            $response = $this->client->request(
                'GET',
                '/v2/cdn/links',
                [
                    'query' => array_replace_recursive([
                        'per_page' => 1000,
                        'paginated' => true,
                        'include_dates' => true,
                    ], $parameter),
                ],
            );

            $this->logger->debug('Response', $response->toArray(false));

            return new LinksResponse($response->toArray());
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }

    public function byParent(Id $parentId): LinksResponse
    {
        try {
            $response = $this->client->request(
                'GET',
                '/v2/cdn/links',
                [
                    'query' => [
                        'per_page' => 1000,
                        'paginated' => true,
                        'include_dates' => true,
                        'with_parent' => $parentId->value,
                    ],
                ],
            );

            $this->logger->debug('Response', $response->toArray(false));

            return new LinksResponse($response->toArray());
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }

    public function roots(): LinksResponse
    {
        try {
            $response = $this->client->request(
                'GET',
                '/v2/cdn/links',
                [
                    'query' => [
                        'per_page' => 1000,
                        'paginated' => true,
                        'include_dates' => true,
                        'with_parent' => 0,
                    ],
                ],
            );

            $this->logger->debug('Response', $response->toArray(false));

            return new LinksResponse($response->toArray());
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }
}
