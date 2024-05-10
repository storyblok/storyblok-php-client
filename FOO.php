<?php

declare(strict_types=1);

namespace App\Bridge\Storyblok\Api;

use App\Bridge\Storyblok\Domain\Value\Link\LinkId;
use App\Bridge\Storyblok\Domain\Value\Response\LinksResponse;
use App\Bridge\Storyblok\Domain\Value\Response\StoriesResponse;
use App\Bridge\Storyblok\Domain\Value\Response\StoryResponse;
use App\Bridge\Storyblok\Domain\Value\Story;
use App\Bridge\Storyblok\Domain\Value\Uuid;
use App\Bridge\Storyblok\Exception\ApiResponseException;
use Storyblok\ApiException;
use Storyblok\Client;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final readonly class StoryblokApi implements StoryblokApiInterface
{
    private const int MAX_LINKS_PER_PAGE = 1000;

    public function __construct(
        private Client $client,
    ) {
    }

    public function getLinks(): array
    {
        $responseBody = $this->doGetLinks();

        return (new LinksResponse($responseBody))->links;
    }

    public function getLinksAtRoot(): array
    {
        $responseBody = $this->doGetLinks([
            'with_parent' => 0,
        ]);

        return (new LinksResponse($responseBody))->links;
    }

    public function getLinksByParentId(LinkId $parentId): array
    {
        $responseBody = $this->doGetLinks([
            'with_parent' => $parentId->value,
        ]);

        return (new LinksResponse($responseBody))->links;
    }

    public function getStories(string $locale = 'en'): array
    {
        $response = $this->client
            ->language($locale)
            ->getStories();

        if (Response::HTTP_OK !== $response->getCode()) {
            throw new ApiResponseException(
                sprintf('Storyblok API returned error code %s', $response->responseCode),
            );
        }

        $responseBody = $response->getBody();
        Assert::isArray($responseBody);

        return (new StoriesResponse($responseBody))->stories;
    }

    public function getStoryBySlug(string $slug, string $locale = 'en'): Story
    {
        $response = $this->client
            ->language($locale)
            ->resolveLinks('url')
            ->getStoryBySlug($slug);

        if (Response::HTTP_OK !== $response->getCode()) {
            throw new ApiResponseException(
                sprintf('Storyblok API returned error code %s', $response->responseCode),
            );
        }

        $responseBody = $response->getBody();
        Assert::isArray($responseBody);

        return (new StoryResponse($responseBody))->story;
    }

    public function getStoryByUuid(Uuid $uuid, string $locale = 'en'): Story
    {
        $response = $this->client
            ->language($locale)
            ->getStoryByUuid($uuid->value);

        if (Response::HTTP_OK !== $response->getCode()) {
            throw new ApiResponseException(
                sprintf('Storyblok API returned error code %s', $response->responseCode),
            );
        }

        $responseBody = $response->getBody();
        Assert::isArray($responseBody);

        return (new StoryResponse($responseBody))->story;
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @return array<string, mixed>
     */
    private function doGetLinks(array $parameters = []): array
    {
        try {
            $response = $this->client->getLinks([
                'per_page' => self::MAX_LINKS_PER_PAGE,
                'include_dates' => true,
                ...$parameters,
            ]);
        } catch (ApiException $e) {
            throw new ApiResponseException(sprintf('Storyblok API exception: "%s".', $e->getMessage()), $e->getCode(), $e);
        }

        if (Response::HTTP_OK !== $response->getCode()) {
            throw new ApiResponseException(sprintf('Unexpected Response Code "%s".', $response->getCode()));
        }

        $responseBody = $response->getBody();
        Assert::isArray($responseBody);

        return $responseBody;
    }
}
