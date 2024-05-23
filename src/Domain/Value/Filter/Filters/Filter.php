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
    abstract public function field(): string;

    abstract public function value(): string;

    abstract public static function operation(): Operation;

    /**
     * @return array<string, bool|float|int|string>
     */
    final public function toArray(): array
    {
        return [
            sprintf('filter_query[%s][%s]', $this->field(), static::operation()->value) => $this->value(),
        ];
    }
}
