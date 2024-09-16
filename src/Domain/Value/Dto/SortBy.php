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

namespace SensioLabs\Storyblok\Api\Domain\Value\Dto;

use OskarStark\Value\TrimmedNonEmptyString;

final readonly class SortBy
{
    public function __construct(
        public string $field,
        public Direction $direction,
    ) {
        TrimmedNonEmptyString::fromString($field);
    }

    public function toString(): string
    {
        return \sprintf('%s:%s', $this->field, $this->direction->value);
    }
}
