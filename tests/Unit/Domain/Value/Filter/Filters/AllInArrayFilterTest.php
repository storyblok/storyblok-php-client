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

use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\AllInArrayFilter;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Operation;
use SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value\Filter\FilterTestCase;

final class AllInArrayFilterTest extends FilterTestCase
{
    public static function operation(): Operation
    {
        return Operation::AllInArray;
    }

    public static function filterClass(): string
    {
        return AllInArrayFilter::class;
    }

    /**
     * @test
     */
    public function field(): void
    {
        $faker = self::faker();
        $filter = new AllInArrayFilter($field = $faker->word(), [$faker->word()]);

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

        new AllInArrayFilter($field, [self::faker()->word()]);
    }

    /**
     * @test
     */
    public function value(): void
    {
        $faker = self::faker();
        $filter = new AllInArrayFilter($faker->word(), [$value = $faker->word()]);

        self::assertSame($value, $filter->value());
    }

    /**
     * @test
     *
     * @dataProvider invalidValues
     */
    public function valueInvalid(mixed $value): void
    {
        self::expectException(\InvalidArgumentException::class);

        new AllInArrayFilter(self::faker()->word(), $value);
    }

    /**
     * @return iterable<string, array<mixed>>
     */
    public static function invalidValues(): iterable
    {
        $faker = self::faker();

        yield 'array is empty' => [[]];
        yield 'array not only contains string' => [[$faker->word(), $faker->randomNumber()]];
        yield 'array contains empty string' => [[$faker->word(), '']];
        yield 'array contains whitespace only string' => [[$faker->word(), ' ']];
    }
}
