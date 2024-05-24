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

use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\Filter;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\InFilter;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\LikeFilter;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\OrFilter;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Operation;
use SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value\Filter\FilterTestCase;

final class OrFilterTest extends FilterTestCase
{
    public static function operation(): Operation
    {
        return Operation::Or;
    }

    public static function filterClass(): string
    {
        return OrFilter::class;
    }

    /**
     * @test
     */
    public function toArray(): void
    {
        $filter = new OrFilter(new InFilter('title', 'Fancy title'), new LikeFilter('title', '*test'));

        self::assertSame([
            Operation::Or->value => [
                [
                    'title' => [
                        Operation::In->value => 'Fancy title',
                    ],
                ],
                [
                    'title' => [
                        Operation::Like->value => '*test',
                    ],
                ],
            ],
        ], $filter->toArray());
    }

    /**
     * @test
     */
    public function field(): void
    {
        $filter = new OrFilter(new InFilter('title', 'Fancy title'), new LikeFilter('title', '*test'));

        self::assertSame('title|title', $filter->field());
    }

    /**
     * @test
     *
     * @dataProvider invalidValues
     *
     * @param list<Filter> $filters
     */
    public function invalid(array $filters): void
    {
        self::expectException(\InvalidArgumentException::class);

        new OrFilter(...$filters);
    }

    /**
     * @return iterable<string, array<mixed>>
     */
    public static function invalidValues(): iterable
    {
        $faker = self::faker();

        yield 'one filter passed' => [[new InFilter($faker->word(), $faker->word())]];
    }

    /**
     * @test
     */
    public function filtersCanBeTheSameForField(): void
    {
        $filter = new OrFilter(
            new LikeFilter('title', 'Fancy title'),
            new LikeFilter('title', '*test'),
        );

        self::assertSame([
            Operation::Or->value => [
                [
                    'title' => [
                        Operation::Like->value => 'Fancy title',
                    ],
                ],
                [
                    'title' => [
                        Operation::Like->value => '*test',
                    ],
                ],
            ],
        ], $filter->toArray());
    }
}
