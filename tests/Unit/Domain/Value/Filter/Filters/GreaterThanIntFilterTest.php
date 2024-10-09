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

use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\GreaterThanIntFilter;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Operation;
use SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value\Filter\FilterTestCase;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class GreaterThanIntFilterTest extends FilterTestCase
{
    public static function operation(): Operation
    {
        return Operation::GreaterThanInt;
    }

    public static function filterClass(): string
    {
        return GreaterThanIntFilter::class;
    }

    public function toArray(): void
    {
        $faker = self::faker();
        $filter = new GreaterThanIntFilter($field = $faker->word(), $value = $faker->randomNumber());

        self::assertSame([
            $field => [
                Operation::GreaterThanInt->value => $value,
            ],
        ], $filter->toArray());
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

        new GreaterThanIntFilter($field, self::faker()->randomNumber());
    }
}
