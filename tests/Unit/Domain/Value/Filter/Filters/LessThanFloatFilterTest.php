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

namespace SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value\Filter\Filters;

use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\LessThanFloatFilter;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Operation;
use SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value\Filter\FilterTestCase;

final class LessThanFloatFilterTest extends FilterTestCase
{
    public static function operation(): Operation
    {
        return Operation::LessThanFloat;
    }

    public static function filterClass(): string
    {
        return LessThanFloatFilter::class;
    }

    /**
     * @test
     */
    public function field(): void
    {
        $faker = self::faker();
        $filter = new LessThanFloatFilter($field = $faker->word(), $faker->randomFloat());

        self::assertSame($field, $filter->field());
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank()
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty()
     */
    public function fieldInvalid(string $field): void
    {
        self::expectException(\InvalidArgumentException::class);

        new LessThanFloatFilter($field, self::faker()->randomFloat());
    }

    /**
     * @test
     */
    public function value(): void
    {
        $faker = self::faker();
        $filter = new LessThanFloatFilter($faker->word(), $value = $faker->randomFloat());

        self::assertSame((string) $value, $filter->value());
    }
}
