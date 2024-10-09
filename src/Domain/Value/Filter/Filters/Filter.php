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

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
abstract readonly class Filter
{
    abstract public static function operation(): Operation;

    final public function equals(self $other): bool
    {
        return $this->toArray() === $other->toArray();
    }

    abstract public function field(): string;

    /**
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;
}
