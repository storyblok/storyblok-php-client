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

namespace SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value\Dto;

use PHPUnit\Framework\TestCase;
use SensioLabs\Storyblok\Api\Domain\Value\Dto\Direction;
use SensioLabs\Storyblok\Api\Domain\Value\Dto\SortBy;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

final class SortByTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function page(): void
    {
        $value = self::faker()->word();

        self::assertSame($value, (new SortBy($value, Direction::Asc))->field);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank()
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty()
     */
    public function pageInvalid(string $value): void
    {
        self::expectException(\InvalidArgumentException::class);

        new SortBy($value, Direction::Asc);
    }
}
