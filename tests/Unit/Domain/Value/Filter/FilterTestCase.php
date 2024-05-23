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

namespace SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value\Filter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Operation;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

abstract class FilterTestCase extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function isSameOperation(): void
    {
        self::assertSame(
            static::operation()->value,
            (static::filterClass())::operation()->value,
        );
    }

    abstract protected static function operation(): Operation;

    /**
     * @return class-string
     */
    abstract protected static function filterClass(): string;
}
