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

use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\InFilter;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Operation;
use SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value\Filter\FilterTestCase;

final class InFilterTest extends FilterTestCase
{
    public static function operation(): Operation
    {
        return Operation::In;
    }

    public static function filterClass(): string
    {
        return InFilter::class;
    }

    public function toArray(): void
    {
        $faker = self::faker();
        $filter = new InFilter($field = $faker->word(), [$value = $faker->word()]);

        self::assertSame([
            $field => [
                Operation::In->value => $value,
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

        new InFilter($field, [self::faker()->word()]);
    }

    /**
     * @test
     *
     * @dataProvider invalidValues
     *
     * @param array<mixed>|string $value
     */
    public function valueInvalid(array|string $value): void
    {
        self::expectException(\InvalidArgumentException::class);

        new InFilter(self::faker()->word(), $value);
    }

    /**
     * @return iterable<string, array<mixed>>
     */
    public static function invalidValues(): iterable
    {
        $faker = self::faker();

        yield 'string is empty' => [''];
        yield 'array is empty' => [[]];
        yield 'array not only contains string' => [[$faker->word(), $faker->randomNumber()]];
        yield 'array contains empty string' => [[$faker->word(), '']];
        yield 'array contains whitespace only string' => [[$faker->word(), ' ']];
    }
}
