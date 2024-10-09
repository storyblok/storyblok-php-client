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

use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\LessThanDateFilter;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Operation;
use SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value\Filter\FilterTestCase;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class LessThanDateFilterTest extends FilterTestCase
{
    public static function operation(): Operation
    {
        return Operation::LessThanDate;
    }

    public static function filterClass(): string
    {
        return LessThanDateFilter::class;
    }

    /**
     * @test
     */
    public function toArray(): void
    {
        $faker = self::faker();
        $filter = new LessThanDateFilter($field = $faker->word(), $value = $faker->dateTime());

        self::assertSame([
            $field => [
                Operation::LessThanDate->value => $value->format('Y-m-d H:i'),
            ],
        ], $filter->toArray());
    }

    /**
     * @test
     */
    public function field(): void
    {
        $faker = self::faker();
        $filter = new LessThanDateFilter($field = $faker->word(), $faker->dateTime());

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

        new LessThanDateFilter($field, self::faker()->dateTime());
    }
}
