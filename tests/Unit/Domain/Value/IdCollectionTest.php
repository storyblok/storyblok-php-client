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

namespace SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value;

use PHPUnit\Framework\TestCase;
use SensioLabs\Storyblok\Api\Domain\Value\Id;
use SensioLabs\Storyblok\Api\Domain\Value\IdCollection;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class IdCollectionTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function add(): void
    {
        $faker = self::faker();

        $collection = new IdCollection();
        self::assertEmpty($collection);

        $collection->add(new Id($faker->numberBetween(1)));
        self::assertCount(1, $collection);
    }

    /**
     * @test
     */
    public function remove(): void
    {
        $faker = self::faker();

        $field = new Id($faker->numberBetween(1));

        $collection = new IdCollection([$field]);
        self::assertCount(1, $collection);

        $collection->remove($field);
        self::assertEmpty($collection);
    }

    /**
     * @test
     */
    public function hasReturnsTrue(): void
    {
        $faker = self::faker();

        $field = new Id($faker->numberBetween(1));

        $collection = new IdCollection([$field, new Id($faker->numberBetween(1))]);

        self::assertTrue($collection->has($field));
    }

    /**
     * @test
     */
    public function hasReturnsFalse(): void
    {
        $faker = self::faker();

        $collection = new IdCollection([new Id($faker->numberBetween(1))]);

        self::assertFalse($collection->has(new Id($faker->numberBetween(1))));
    }

    /**
     * @test
     */
    public function isCountable(): void
    {
        $faker = self::faker();

        $field = new Id($faker->numberBetween(1));

        $collection = new IdCollection([$field]);

        self::assertSame(1, $collection->count());
    }

    /**
     * @test
     */
    public function toStringMethod(): void
    {
        $fields = [
            new Id(1),
            new Id(2),
            new Id(3),
        ];

        $collection = new IdCollection($fields);

        self::assertSame('1,2,3', $collection->toString());
    }

    /**
     * @test
     */
    public function getIterator(): void
    {
        $fields = [
            new Id(1),
            new Id(2),
        ];

        self::assertInstanceOf(\ArrayIterator::class, (new IdCollection($fields))->getIterator());
    }
}
