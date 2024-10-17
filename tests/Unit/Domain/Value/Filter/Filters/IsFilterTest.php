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

use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\IsFilter;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Operation;
use SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value\Filter\FilterTestCase;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 */
final class IsFilterTest extends FilterTestCase
{
    public static function operation(): Operation
    {
        return Operation::Is;
    }

    public static function filterClass(): string
    {
        return IsFilter::class;
    }

    public function toArray(): void
    {
        $faker = self::faker();
        $filter = new IsFilter($field = $faker->word(), $value = $faker->randomElement([
            IsFilter::EMPTY_ARRAY,
            IsFilter::NOT_EMPTY_ARRAY,
            IsFilter::EMPTY,
            IsFilter::NOT_EMPTY,
            IsFilter::TRUE,
            IsFilter::FALSE,
            IsFilter::NULL,
            IsFilter::NOT_NULL,
        ]));

        self::assertSame([
            $field => [
                Operation::Is->value => $value,
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

        new IsFilter($field, IsFilter::EMPTY);
    }

    /**
     * @test
     *
     * @dataProvider invalidValues
     */
    public function valueInvalid(mixed $value): void
    {
        self::expectException(\InvalidArgumentException::class);

        new IsFilter(self::faker()->word(), $value);
    }

    /**
     * @return iterable<string, array<mixed>>
     */
    public static function invalidValues(): iterable
    {
        $faker = self::faker();

        yield 'is not one of the accepted values' => [$faker->word()];
    }
}
