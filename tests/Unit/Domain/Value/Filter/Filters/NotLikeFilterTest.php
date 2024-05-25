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

use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\NotLikeFilter;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Operation;
use SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value\Filter\FilterTestCase;

final class NotLikeFilterTest extends FilterTestCase
{
    public static function operation(): Operation
    {
        return Operation::NotLike;
    }

    public static function filterClass(): string
    {
        return NotLikeFilter::class;
    }

    /**
     * @test
     */
    public function toArray(): void
    {
        $faker = self::faker();
        $filter = new NotLikeFilter($field = $faker->word(), $value = $faker->word());

        self::assertSame([
            $field => [
                Operation::NotLike->value => $value,
            ],
        ], $filter->toArray());
    }

    /**
     * @test
     */
    public function field(): void
    {
        $faker = self::faker();
        $filter = new NotLikeFilter($field = $faker->word(), $faker->word());

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

        new NotLikeFilter($field, self::faker()->word());
    }
}
