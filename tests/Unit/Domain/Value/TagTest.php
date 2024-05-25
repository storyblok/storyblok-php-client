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
use SensioLabs\Storyblok\Api\Domain\Value\Tag;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

final class TagTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function name(): void
    {
        $value = self::faker()->word();
        $tag = new Tag($value, 0);

        self::assertSame($value, $tag->name);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty()
     */
    public function nameInvalid(string $value): void
    {
        self::expectException(\InvalidArgumentException::class);

        new Tag($value, 0);
    }

    /**
     * @test
     */
    public function taggingsCount(): void
    {
        $word = self::faker()->word();
        $value = self::faker()->numberBetween(1, 10000);

        self::assertSame($value, (new Tag($word, $value))->taggingsCount);
    }

    /**
     * @test
     */
    public function taggingsCountCanBeZero(): void
    {
        $word = self::faker()->word();

        self::assertSame(0, (new Tag($word, 0))->taggingsCount);
    }

    /**
     * @test
     */
    public function taggingCountMustBeGreaterThanOrEqualZero(): void
    {
        $word = self::faker()->word();
        self::expectException(\InvalidArgumentException::class);

        new Tag($word, -1);
    }
}
