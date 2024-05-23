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

namespace SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters;

use OskarStark\Value\TrimmedNonEmptyString;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Operation;

final readonly class GreaterThanDateFilter extends Filter
{
    public function __construct(
        private string $field,
        private \DateTimeInterface $value,
    ) {
        TrimmedNonEmptyString::fromString($field);
    }

    public function field(): string
    {
        return $this->field;
    }

    public function value(): string
    {
        return $this->value->format('Y-m-d H:i');
    }

    public static function operation(): Operation
    {
        return Operation::GreaterThanDate;
    }
}
