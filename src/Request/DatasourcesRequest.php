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
use Webmozart\Assert\Assert;

final readonly class DatasourcesRequest
{
    public const int MAX_PER_PAGE = 1000;
    public const int PER_PAGE = 25;

    public function __construct(
        public Pagination $pagination = new Pagination(perPage: self::PER_PAGE),
    ) {
        Assert::lessThanEq($this->pagination->perPage, self::MAX_PER_PAGE);
    }

    /**
     * @return array{
     *     page: int,
     *     per_page: int,
     * }
     */
    public function toArray(): array
    {
        return [
            'page' => $this->pagination->page,
            'per_page' => $this->pagination->perPage,
        ];
    }
}
