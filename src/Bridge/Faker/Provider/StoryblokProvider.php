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

namespace SensioLabs\Storyblok\Api\Bridge\Faker\Provider;

use Faker\Provider\Base as BaseProvider;
use function Safe\array_replace_recursive;

final class StoryblokProvider extends BaseProvider
{
    /**
     * @param array{
     *      cv?: integer,
     *      stories?: list<array<string, mixed>>,
     *      links?: string[],
     *      rels?: string[],
     *  } $overrides
     *
     * @return array{
     *     cv: integer,
     *     stories: list<array<string, mixed>>,
     *     links: string[],
     *     rels: string[],
     * }
     */
    public function storiesResponse(array $overrides = []): array
    {
        $response = [
            'stories' => [],
            'cv' => $this->generator->randomNumber(),
            'rels' => [],
            'links' => [],
        ];

        for ($i = 0; $this->generator->numberBetween(1, 5) > $i; ++$i) {
            $response['stories'][] = [
                'uuid' => $this->generator->uuid(),
            ];
        }

        for ($i = 0; $this->generator->numberBetween(1, 5) > $i; ++$i) {
            $response['rels'][] = $this->generator->url();
        }

        for ($i = 0; $this->generator->numberBetween(1, 5) > $i; ++$i) {
            $response['links'][] = $this->generator->url();
        }

        return array_replace_recursive(
            $response,
            $overrides,
        );
    }

    /**
     * @param array{
     *      cv?: integer,
     *      story?: array<string, mixed>,
     *      links?: string[],
     *      rels?: string[],
     *  } $overrides
     *
     * @return array{
     *     cv: integer,
     *     story: array<string, mixed>,
     *     links: string[],
     *     rels: string[],
     * }
     */
    public function storyResponse(array $overrides = []): array
    {
        $response = [
            'story' => [
                'uuid' => $this->generator->uuid(),
            ],
            'cv' => $this->generator->randomNumber(),
            'rels' => [],
            'links' => [],
        ];

        for ($i = 0; $this->generator->numberBetween(1, 5) > $i; ++$i) {
            $response['rels'][] = $this->generator->url();
        }

        for ($i = 0; $this->generator->numberBetween(1, 5) > $i; ++$i) {
            $response['links'][] = $this->generator->url();
        }

        return array_replace_recursive(
            $response,
            $overrides,
        );
    }

    /**
     * @param array{
     *      links?: list<array<string, mixed>>,
     *  } $overrides
     *
     * @return array{
     *      links: list<array<string, mixed>>,
     * }
     */
    public function linksResponse(array $overrides = []): array
    {
        $response = [
            'links' => [],
        ];

        for ($i = 0; $this->generator->numberBetween(1, 10) > $i; ++$i) {
            $response['links'][] = $this->linkResponse();
        }

        return array_replace_recursive(
            $response,
            $overrides,
        );
    }

    /**
     * @param array{
     *     uuid?: string,
     *     id?: integer,
     *     parent_id?: integer|null,
     *     name?: string,
     *     slug?: string,
     *     path?: string,
     *     real_path?: string,
     *     is_folder?: boolean,
     *     published?: boolean,
     *     is_startpage?: boolean,
     *     position?: integer
     * } $overrides
     *
     * @return array{
     *     uuid: string,
     *     id: integer,
     *     parent_id: integer|null,
     *     name: string,
     *     slug: string,
     *     path: string,
     *     real_path: string,
     *     is_folder: boolean,
     *     published: boolean,
     *     is_startpage: boolean,
     *     position: integer,
     *     alternates: list<array<string,mixed>>
     * }
     */
    public function linkResponse(array $overrides = []): array
    {
        $response = [
            'uuid' => $this->generator->uuid(),
            'id' => $this->generator->numberBetween(1, 1000000),
            'parent_id' => null,
            'name' => $this->generator->sentence(),
            'slug' => $this->generator->slug(),
            'path' => $this->generator->slug(),
            'real_path' => $this->generator->slug(),
            'is_folder' => false,
            'published' => true,
            'is_startpage' => false,
            'position' => 0,
        ];

        $response['alternates'] = [];

        foreach (['de', 'fr'] as $lang) {
            $response['alternates'][] = $this->linkAlternateResponse(['lang' => $lang]);
        }

        return array_replace_recursive(
            $response,
            $overrides,
        );
    }

    /**
     * @param array{
     *      lang?: string,
     *      name?: string,
     *      path?: string,
     *      published?: boolean,
     *      translated_slug?: string
     * } $overrides
     *
     * @return array{
     *     lang: string,
     *     name: string,
     *     path: string,
     *     published: boolean,
     *     translated_slug: string
     * }
     */
    public function linkAlternateResponse(array $overrides = []): array
    {
        $response = [
            'lang' => $this->generator->randomElement(['de', 'fr']),
            'name' => $this->generator->sentence(),
            'path' => $this->generator->slug(),
            'published' => true,
            'translated_slug' => $this->generator->slug(),
        ];

        return array_replace_recursive(
            $response,
            $overrides,
        );
    }

    /**
     * @param array{
     *    tags?: list<array{name?: string, taggings_count?: int}>,
     * } $overrides
     *
     * @return array{
     *    tags: list<array{name: string, taggings_count: int}>,
     * }
     */
    public function tagsResponse(array $overrides = []): array
    {
        $response = [
            'tags' => [],
        ];

        for ($i = 0; $this->generator->numberBetween(1, 10) > $i; ++$i) {
            $response['tags'][] = [
                'name' => $this->generator->word(),
                'taggings_count' => $this->generator->numberBetween(0, 100),
            ];
        }

        return array_replace_recursive(
            $response,
            $overrides,
        );
    }

    /**
     * @param array{
     *   datasource_entries?: list<array{
     *     id?: int,
     *     name?: string,
     *     value?: string,
     *     dimension_value?: string|null
     * }>,
     * } $overrides
     *
     * @return array{
     *   datasource_entries: list<array{
     *     id: int,
     *     name: string,
     *     value: string,
     *     dimension_value: string|null
     *   }>,
     * }
     */
    public function datasourceResponse(array $overrides = []): array
    {
        $response = [
            'datasource_entries' => [],
        ];

        for ($i = 0; $this->generator->numberBetween(1, 10) > $i; ++$i) {
            $response['datasource_entries'][] = $this->datasourceEntryResponse();
        }

        return array_replace_recursive(
            $response,
            $overrides,
        );
    }

    /**
     * @param array{
     *     id?: int,
     *     name?: string,
     *     value?: string,
     *     dimension_value?: string|null
     * } $overrides
     *
     * @return array{
     *     id: int,
     *     name: string,
     *     value: string,
     *     dimension_value: string|null
     * }
     */
    public function datasourceEntryResponse(array $overrides = []): array
    {
        $response = [
            'id' => $this->generator->numberBetween(1),
            'name' => $this->generator->word(),
            'value' => $this->generator->word(),
            'dimension_value' => $this->generator->boolean() ? $this->generator->word() : null,
        ];

        return array_replace_recursive(
            $response,
            $overrides,
        );
    }
}
