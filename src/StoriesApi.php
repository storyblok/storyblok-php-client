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
use SensioLabs\Storyblok\Api\Domain\Value\Uuid;
use SensioLabs\Storyblok\Api\Response\StoriesResponse;
use SensioLabs\Storyblok\Api\Response\StoryResponse;
use Webmozart\Assert\Assert;

final class StoriesApi implements StoriesApiInterface
{
    public function __construct(
        private StoryblokClientInterface $client,
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function all(string $locale = 'default'): StoriesResponse
    {
        Assert::stringNotEmpty($locale);

        try {
            $response = $this->client->request(
                'GET',
                '/v2/cdn/stories',
                [
                    'query' => [
                        'language' => $locale,
                        'fallback_lang' => 'default',
                    ],
                ],
            );

            $this->logger->debug('Response', $response->toArray(false));

            return new StoriesResponse($response->toArray());
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }

    public function bySlug(string $slug, string $locale = 'default'): StoryResponse
    {
        Assert::stringNotEmpty($locale);
        Assert::stringNotEmpty($slug);

        try {
            $response = $this->client->request(
                'GET',
                sprintf('/v2/cdn/stories/%s', $slug),
                [
                    'query' => [
                        'language' => $locale,
                        'fallback_lang' => 'default',
                    ],
                ],
            );

            $this->logger->debug('Response', $response->toArray(false));

            return new StoryResponse($response->toArray());
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }

    public function allByContentType(string $contentType, string $locale = 'default'): StoriesResponse
    {
        Assert::stringNotEmpty($contentType);
        Assert::stringNotEmpty($locale);

        try {
            $response = $this->client->request(
                'GET',
                '/v2/cdn/stories',
                [
                    'query' => [
                        'language' => $locale,
                        'fallback_lang' => 'default',
                        'content_type' => $contentType,
                    ],
                ],
            );

            $this->logger->debug('Response', $response->toArray(false));

            return new StoriesResponse($response->toArray());
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }

    public function byUuid(Uuid $uuid, string $locale = 'default'): StoryResponse
    {
        Assert::stringNotEmpty($locale);

        try {
            $response = $this->client->request(
                'GET',
                sprintf('/v2/cdn/stories/%s', $uuid->value),
                [
                    'query' => [
                        'language' => $locale,
                        'fallback_lang' => 'default',
                        'find_by' => 'uuid',
                    ],
                ],
            );

            $this->logger->debug('Response', $response->toArray(false));

            return new StoryResponse($response->toArray());
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }

    public function byId(Id $id, string $locale = 'default'): StoryResponse
    {
        Assert::stringNotEmpty($locale);

        try {
            $response = $this->client->request(
                'GET',
                sprintf('/v2/cdn/stories/%s', $id->value),
                [
                    'query' => [
                        'language' => $locale,
                        'fallback_lang' => 'default',
                    ],
                ],
            );

            $this->logger->debug('Response', $response->toArray(false));

            return new StoryResponse($response->toArray());
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }
}
