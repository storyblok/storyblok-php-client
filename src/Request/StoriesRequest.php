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

namespace SensioLabs\Storyblok\Api\Request;

use SensioLabs\Storyblok\Api\Domain\Value\Dto\Pagination;
use SensioLabs\Storyblok\Api\Domain\Value\Dto\SortBy;
use SensioLabs\Storyblok\Api\Domain\Value\Dto\Version;
use SensioLabs\Storyblok\Api\Domain\Value\Field\FieldCollection;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\FilterCollection;
use SensioLabs\Storyblok\Api\Domain\Value\IdCollection;
use SensioLabs\Storyblok\Api\Domain\Value\Tag\TagCollection;
use Webmozart\Assert\Assert;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final readonly class StoriesRequest
{
    public const int MAX_PER_PAGE = 100;
    public const int PER_PAGE = 25;

    public function __construct(
        public string $language = 'default',
        public Pagination $pagination = new Pagination(perPage: self::PER_PAGE),
        public ?SortBy $sortBy = null,
        public ?FilterCollection $filters = null,
        public ?FieldCollection $excludeFields = null,
        public ?TagCollection $withTags = null,
        public ?IdCollection $excludeIds = null,
        public ?Version $version = null,
    ) {
        Assert::stringNotEmpty($language);
        Assert::lessThanEq($this->pagination->perPage, self::MAX_PER_PAGE);
    }

    /**
     * @return array{
     *     language: string,
     *     page: int,
     *     per_page: int,
     *     sort_by?: string,
     *     filter_query?: list<mixed>,
     *     with_tag?: string,
     *     excluding_fields?: string,
     *     excluding_ids?: string,
     *     version?: string,
     * }
     */
    public function toArray(): array
    {
        $array = [
            'language' => $this->language,
            'page' => $this->pagination->page,
            'per_page' => $this->pagination->perPage,
        ];

        if (null !== $this->sortBy) {
            $array['sort_by'] = $this->sortBy->toString();
        }

        if (null !== $this->filters && $this->filters->count() > 0) {
            $array['filter_query'] = $this->filters->toArray();
        }

        if (null !== $this->withTags && $this->withTags->count() > 0) {
            $array['with_tag'] = $this->withTags->toString();
        }

        if (null !== $this->excludeFields && $this->excludeFields->count() > 0) {
            $array['excluding_fields'] = $this->excludeFields->toString();
        }

        if (null !== $this->excludeIds && $this->excludeIds->count() > 0) {
            $array['excluding_ids'] = $this->excludeIds->toString();
        }

        if (null !== $this->version) {
            $array['version'] = $this->version->value;
        }

        return $array;
    }
}
