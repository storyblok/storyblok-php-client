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

final readonly class Tag
{
    public function __construct(
        public string $name,
        public int $taggingsCount,
    ) {
        Assert::stringNotEmpty($this->name);
        Assert::greaterThanEq($this->taggingsCount, 0);
    }
}
