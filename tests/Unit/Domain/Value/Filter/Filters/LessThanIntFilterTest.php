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

use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\LessThanIntFilter;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Operation;
use SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value\Filter\FilterTestCase;

final class LessThanIntFilterTest extends FilterTestCase
{
    public static function operation(): Operation
    {
        return Operation::LessThanInt;
    }

    public static function filterClass(): string
    {
        return LessThanIntFilter::class;
    }

    /**
     * @test
     */
    public function field(): void
    {
        $faker = self::faker();
        $filter = new LessThanIntFilter($field = $faker->word(), $faker->randomNumber());

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

        new LessThanIntFilter($field, self::faker()->randomNumber());
    }

    /**
     * @test
     */
    public function value(): void
    {
        $faker = self::faker();
        $filter = new LessThanIntFilter($faker->word(), $value = $faker->randomNumber());

        self::assertSame((string) $value, $filter->value());
    }

    /**
     * @test
     *
     * @dataProvider invalidValues
     */
    public function valueInvalid(mixed $value): void
    {
        self::expectException(\InvalidArgumentException::class);

        new LessThanIntFilter(self::faker()->word(), $value);
    }

    /**
     * @return iterable<string, array<mixed>>
     */
    public static function invalidValues(): iterable
    {
        $faker = self::faker();

        yield 'is not int' => [$faker->word()];
    }
}
