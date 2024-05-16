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

namespace SensioLabs\Storyblok\Api\Domain\Value\Datasource;

use OskarStark\Value\TrimmedNonEmptyString;

final readonly class Dimension
{
    public string $value;

    public function __construct(?string $value = null)
    {
        if (null === $value) {
            $this->value = 'default';
        } else {
            $this->value = TrimmedNonEmptyString::fromString($value)->toString();
        }
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
