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
use SensioLabs\Storyblok\Api\Domain\Value\Dto\Version;
use Webmozart\Assert\Assert;

final readonly class LinksRequest
{
    public const int MAX_PER_PAGE = 1000;
    public const int PER_PAGE = 25;

    public function __construct(
        public Pagination $pagination = new Pagination(perPage: self::PER_PAGE),
        public bool $includeDates = true,
        public ?Version $version = null,
    ) {
        Assert::lessThanEq($this->pagination->perPage, self::MAX_PER_PAGE);
    }

    /**
     * @return array{
     *     page: int,
     *     per_page: int,
     *     include_dates: bool,
     *     version?: string,
     * }
     */
    public function toArray(): array
    {
        $values = [
            'page' => $this->pagination->page,
            'per_page' => $this->pagination->perPage,
            'include_dates' => $this->includeDates,
        ];

        if (null !== $this->version) {
            $values['version'] = $this->version->value;
        }

        return $values;
    }
}
