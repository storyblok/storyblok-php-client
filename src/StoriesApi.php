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

use SensioLabs\Storyblok\Api\Domain\Value\Dto\Version;
use SensioLabs\Storyblok\Api\Domain\Value\Id;
use SensioLabs\Storyblok\Api\Domain\Value\Total;
use SensioLabs\Storyblok\Api\Domain\Value\Uuid;
use SensioLabs\Storyblok\Api\Request\StoriesRequest;
use SensioLabs\Storyblok\Api\Response\StoriesResponse;
use SensioLabs\Storyblok\Api\Response\StoryResponse;
use Webmozart\Assert\Assert;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Simon André <smn.andre@gmail.com>
 */
final class StoriesApi implements StoriesApiInterface
{
    private const string ENDPOINT = '/v2/cdn/stories';
    private Version $version;

    /**
     * @param 'draft'|'published' $version
     */
    public function __construct(
        private StoryblokClientInterface $client,
        string $version = 'published', // we inject a string here, because Symfony DI does not support enums
    ) {
        $this->version = Version::from($version);
    }

    public function all(?StoriesRequest $request = null): StoriesResponse
    {
        $request ??= new StoriesRequest();

        $response = $this->client->request('GET', self::ENDPOINT, [
            'query' => $request->toArray(),
        ]);

        return new StoriesResponse(
            Total::fromHeaders($response->getHeaders()),
            $request->pagination,
            $response->toArray(),
        );
    }

    public function allByContentType(string $contentType, ?StoriesRequest $request = null): StoriesResponse
    {
        Assert::stringNotEmpty($contentType);

        $request ??= new StoriesRequest();

        $response = $this->client->request('GET', self::ENDPOINT, [
            'query' => [
                ...$request->toArray(),
                'content_type' => $contentType,
            ],
        ]);

        return new StoriesResponse(
            Total::fromHeaders($response->getHeaders()),
            $request->pagination,
            $response->toArray(),
        );
    }

    public function bySlug(string $slug, string $language = 'default', ?Version $version = null): StoryResponse
    {
        Assert::stringNotEmpty($language);
        Assert::stringNotEmpty($slug);

        $response = $this->client->request('GET', \sprintf('%s/%s', self::ENDPOINT, $slug), [
            'query' => [
                'language' => $language,
                'version' => null !== $version ? $version->value : $this->version->value,
            ],
        ]);

        return new StoryResponse($response->toArray());
    }

    public function byUuid(Uuid $uuid, string $language = 'default', ?Version $version = null): StoryResponse
    {
        Assert::stringNotEmpty($language);

        $response = $this->client->request('GET', \sprintf('%s/%s', self::ENDPOINT, $uuid->value), [
            'query' => [
                'language' => $language,
                'find_by' => 'uuid',
                'version' => null !== $version ? $version->value : $this->version->value,
            ],
        ]);

        return new StoryResponse($response->toArray());
    }

    public function byId(Id $id, string $language = 'default', ?Version $version = null): StoryResponse
    {
        Assert::stringNotEmpty($language);

        $response = $this->client->request('GET', \sprintf('/v2/cdn/stories/%s', $id->value), [
            'query' => [
                'language' => $language,
                'version' => null !== $version ? $version->value : $this->version->value,
            ],
        ]);

        return new StoryResponse($response->toArray());
    }
}
