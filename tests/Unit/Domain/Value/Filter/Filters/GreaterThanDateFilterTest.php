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

use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\GreaterThanDateFilter;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Operation;
use SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value\Filter\FilterTestCase;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class GreaterThanDateFilterTest extends FilterTestCase
{
    public static function operation(): Operation
    {
        return Operation::GreaterThanDate;
    }

    public static function filterClass(): string
    {
        return GreaterThanDateFilter::class;
    }

    /**
     * @test
     */
    public function toArray(): void
    {
        $faker = self::faker();
        $filter = new GreaterThanDateFilter($field = $faker->word(), $value = $faker->dateTime());

        self::assertSame([
            $field => [
                Operation::GreaterThanDate->value => $value->format('Y-m-d H:i'),
            ],
        ], $filter->toArray());
    }

    /**
     * @test
     */
    public function field(): void
    {
        $faker = self::faker();
        $filter = new GreaterThanDateFilter($field = $faker->word(), $faker->dateTime());

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

        new GreaterThanDateFilter($field, self::faker()->dateTime());
    }
}
