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

use SensioLabs\Storyblok\Api\Domain\Value\Filter\Operation;

abstract readonly class Filter
{
    abstract public static function operation(): Operation;

    final public function equals(self $filter): bool
    {
        return $filter->toArray() === $filter->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;
}
