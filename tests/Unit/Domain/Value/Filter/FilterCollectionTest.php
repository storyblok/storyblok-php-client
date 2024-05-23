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

namespace SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value\Filter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\FilterCollection;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\IsFilter;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

final class FilterCollectionTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function add(): void
    {
        $faker = self::faker();

        $collection = new FilterCollection();
        self::assertEmpty($collection);

        $collection->add(new IsFilter($faker->word(), IsFilter::EMPTY));
        self::assertCount(1, $collection);
    }

    /**
     * @test
     */
    public function remove(): void
    {
        $faker = self::faker();

        $filter = new IsFilter($faker->word(), IsFilter::EMPTY);

        $collection = new FilterCollection([$filter]);
        self::assertCount(1, $collection);

        $collection->remove($filter);
        self::assertEmpty($collection);
    }

    /**
     * @test
     */
    public function has(): void
    {
        $faker = self::faker();

        $filter = new IsFilter($faker->word(), IsFilter::EMPTY);

        $collection = new FilterCollection([$filter]);

        self::assertTrue($collection->has($filter));
    }

    /**
     * @test
     */
    public function isCountable(): void
    {
        $faker = self::faker();

        $filter = new IsFilter($faker->word(), IsFilter::EMPTY);

        $collection = new FilterCollection([$filter]);

        self::assertSame(1, $collection->count());
    }

    /**
     * @test
     */
    public function toArray(): void
    {
        $faker = self::faker();

        $filters = [
            new IsFilter($faker->word(), IsFilter::EMPTY),
            new IsFilter($faker->word(), IsFilter::NOT_EMPTY_ARRAY),
            new IsFilter($faker->word(), IsFilter::FALSE),
        ];

        $collection = new FilterCollection($filters);

        self::assertSame($filters, $collection->toArray());
    }
}
