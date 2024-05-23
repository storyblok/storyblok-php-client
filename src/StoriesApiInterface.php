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

use SensioLabs\Storyblok\Api\Domain\Value\Dto\Pagination;
use SensioLabs\Storyblok\Api\Domain\Value\Dto\SortBy;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\Filter;
use SensioLabs\Storyblok\Api\Domain\Value\Id;
use SensioLabs\Storyblok\Api\Domain\Value\Uuid;
use SensioLabs\Storyblok\Api\Response\StoriesResponse;
use SensioLabs\Storyblok\Api\Response\StoryResponse;

/**
 * @author Silas Joisten <silas.joisten@proton.me>
 */
interface StoriesApiInterface
{
    public const int MAX_PER_PAGE = 100;

    /**
     * @param list<Filter> $filters
     */
    public function all(string $locale = 'default', ?Pagination $pagination = null, ?SortBy $sortBy = null, array $filters = []): StoriesResponse;

    /**
     * @param list<Filter> $filters
     */
    public function allByContentType(string $contentType, string $locale = 'default', ?Pagination $pagination = null, ?SortBy $sortBy = null, array $filters = []): StoriesResponse;

    public function bySlug(string $slug, string $locale = 'default'): StoryResponse;

    public function byUuid(Uuid $uuid, string $locale = 'default'): StoryResponse;

    public function byId(Id $id, string $locale = 'default'): StoryResponse;
}
