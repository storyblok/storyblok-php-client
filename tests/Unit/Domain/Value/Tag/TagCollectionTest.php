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

namespace SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value\Tag;

use PHPUnit\Framework\TestCase;
use SensioLabs\Storyblok\Api\Domain\Value\Tag\Tag;
use SensioLabs\Storyblok\Api\Domain\Value\Tag\TagCollection;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

final class TagCollectionTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function add(): void
    {
        $faker = self::faker();

        $collection = new TagCollection();
        self::assertEmpty($collection);

        $collection->add(new Tag($faker->word()));
        self::assertCount(1, $collection);
    }

    /**
     * @test
     */
    public function remove(): void
    {
        $faker = self::faker();

        $tag = new Tag($faker->word());

        $collection = new TagCollection([$tag]);
        self::assertCount(1, $collection);

        $collection->remove($tag);
        self::assertEmpty($collection);
    }

    /**
     * @test
     */
    public function hasReturnsTrue(): void
    {
        $faker = self::faker();

        $tag = new Tag($faker->word());

        $collection = new TagCollection([$tag, new Tag($faker->word())]);

        self::assertTrue($collection->has($tag));
    }

    /**
     * @test
     */
    public function hasReturnsFalse(): void
    {
        $faker = self::faker();

        $collection = new TagCollection([new Tag($faker->word())]);

        self::assertFalse($collection->has(new Tag($faker->word())));
    }

    /**
     * @test
     */
    public function isCountable(): void
    {
        $faker = self::faker();

        $tag = new Tag($faker->word());

        $collection = new TagCollection([$tag]);

        self::assertSame(1, $collection->count());
    }

    /**
     * @test
     */
    public function toStringMethod(): void
    {
        $tags = [
            new Tag('field'),
            new Tag('title'),
            new Tag('description'),
        ];

        $collection = new TagCollection($tags);

        self::assertSame('field,title,description', $collection->toString());
    }

    /**
     * @test
     */
    public function getIterator(): void
    {
        $tags = [
            new Tag('title'),
            new Tag('title'),
        ];

        self::assertInstanceOf(\ArrayIterator::class, (new TagCollection($tags))->getIterator());
    }
}
