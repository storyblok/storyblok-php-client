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

use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\LikeFilter;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Operation;
use SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value\Filter\FilterTestCase;

final class LikeFilterTest extends FilterTestCase
{
    public static function operation(): Operation
    {
        return Operation::Like;
    }

    public static function filterClass(): string
    {
        return LikeFilter::class;
    }

    public function toArray(): void
    {
        $faker = self::faker();
        $filter = new LikeFilter($field = $faker->word(), $value = $faker->word());

        self::assertSame([
            $field => [
                Operation::Like->value => $value,
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

        new LikeFilter($field, self::faker()->word());
    }
}
