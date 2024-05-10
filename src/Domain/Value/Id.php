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

namespace SensioLabs\Storyblok\Api\Domain\Value;

use Webmozart\Assert\Assert;

final readonly class Id
{
    public function __construct(public int $value)
    {
        Assert::true(0 < $value, 'Link Id must be greater than 0');
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
