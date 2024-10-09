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

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Oskar Stark <oskarstark@googlemail.com>
 * @author Simon André <smn.andre@gmail.com>
 */
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
     *      datasources?: list<array<string, mixed>>,
     *  } $overrides
     *
     * @return array{
     *     datasources: list<array<string, mixed>>,
     * }
     */
    public function datasourcesResponse(array $overrides = []): array
    {
        $response = [
            'datasources' => [],
        ];

        for ($i = 0; $this->generator->numberBetween(1, 5) > $i; ++$i) {
            $response['datasources'][] = $this->datasourceResponse();
        }

        return array_replace_recursive(
            $response,
            $overrides,
        );
    }

    /**
     * @param array{
     *   datasource?: list<array{
     *     id?: int,
     *     name?: string,
     *     slug?: string,
     *     dimensions?: list<array{
     *      id?: int,
     *      name?: string,
     *      entry_value?: string,
     *      datasource_id?: int,
     *      created_at?: string,
     *      updated_at?: string,
     *     }>,
     * }>,
     * } $overrides
     *
     * @return array{
     *   id: int,
     *   name: string,
     *   slug: string,
     *   dimensions: list<array{
     *     id: int,
     *     name: string,
     *     entry_value: string,
     *     datasource_id: int,
     *     created_at: string,
     *     updated_at: string,
     *   }>,
     * }
     */
    public function datasourceResponse(array $overrides = []): array
    {
        $response = [
            'id' => $id = $this->generator->numberBetween(1),
            'name' => $this->generator->word(),
            'slug' => $this->generator->slug(),
            'dimensions' => [],
        ];

        for ($i = 0; $this->generator->numberBetween(0, 3) > $i; ++$i) {
            $response['dimensions'][] = $this->datasourceDimensionResponse(['datasource_id' => $id]);
        }

        return array_replace_recursive(
            $response,
            $overrides,
        );
    }

    /**
     * @param array{
     *    id?: int,
     *    name?: string,
     *    entry_value?: string,
     *    datasource_id?: int,
     *    created_at?: string,
     *    updated_at?: string,
     * } $overrides
     *
     * @return array{
     *     id: int,
     *     name: string,
     *     entry_value: string,
     *     datasource_id: int,
     *     created_at: string,
     *     updated_at: string,
     * }
     */
    public function datasourceDimensionResponse(array $overrides = []): array
    {
        $response = [
            'id' => $this->generator->numberBetween(1),
            'name' => $this->generator->word(),
            'entry_value' => $this->generator->slug(),
            'datasource_id' => $this->generator->numberBetween(1),
            'created_at' => $this->generator->dateTimeThisYear()->format('Y-m-d\TH:i:s.v\Z'),
            'updated_at' => $this->generator->dateTimeThisYear()->format('Y-m-d\TH:i:s.v\Z'),
        ];

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

    /**
     * @param array{
     *     id?: int,
     *     name?: string,
     *     domain?: string,
     *     version?: int,
     *     language_codes?: list<string>
     * } $overrides
     *
     * @return array{
     *     id: int,
     *     name: string,
     *     domain: string,
     *     version: int,
     *     language_codes: list<string>
     * }
     */
    public function spaceResponse(array $overrides = []): array
    {
        $response = [
            'id' => $this->generator->numberBetween(1),
            'name' => $this->generator->word(),
            'domain' => $this->generator->url(),
            'version' => $this->generator->numberBetween(1),
            'language_codes' => ['de', 'fr'],
        ];

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
    public function datasourceEntriesResponse(array $overrides = []): array
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
     *   asset?: array{
     *      filename?: string,
     *      created_at?: string,
     *      updated_at?: string,
     *      expire_at?: null|string,
     *      content_length?: int,
     *      signed_url?: string,
     *      content_type?: string,
     *   },
     * } $overrides
     *
     * @return array{
     *   asset: array{
     *     filename: string,
     *     created_at: string,
     *     updated_at: string,
     *     expire_at: null|string,
     *     content_length: int,
     *     signed_url: string,
     *     content_type: string,
     *   },
     * }
     */
    public function assetResponse(array $overrides = []): array
    {
        $response = [
            'asset' => [
                'filename' => $this->generator->url(),
                'created_at' => $this->generator->dateTime()->format(\DATE_ATOM),
                'updated_at' => $this->generator->dateTime()->format(\DATE_ATOM),
                'expire_at' => $this->generator->boolean() ? $this->generator->dateTime()->format(\DATE_ATOM) : null,
                'content_length' => $this->generator->numberBetween(1),
                'signed_url' => $this->generator->url(),
                'content_type' => $this->generator->word(),
            ],
        ];

        return array_replace_recursive(
            $response,
            $overrides,
        );
    }
}
