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
use SensioLabs\Storyblok\Api\Domain\Value\LinkAlternate;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class LinkAlternateTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function lang(): void
    {
        $values = self::faker()->linkAlternateResponse();

        self::assertSame($values['lang'], (new LinkAlternate($values))->lang);
    }

    /**
     * @test
     */
    public function langKeyMustExist(): void
    {
        $values = self::faker()->linkAlternateResponse();
        unset($values['lang']);

        self::expectException(\InvalidArgumentException::class);

        new LinkAlternate($values);
    }

    /**
     * @test
     */
    public function name(): void
    {
        $values = self::faker()->linkAlternateResponse();

        self::assertSame($values['name'], (new LinkAlternate($values))->name);
    }

    /**
     * @test
     */
    public function nameKeyMustExist(): void
    {
        $values = self::faker()->linkAlternateResponse();
        unset($values['name']);

        self::expectException(\InvalidArgumentException::class);

        new LinkAlternate($values);
    }

    /**
     * @test
     */
    public function path(): void
    {
        $values = self::faker()->linkAlternateResponse();

        self::assertSame($values['path'], (new LinkAlternate($values))->path);
    }

    /**
     * @test
     */
    public function pathKeyMustExist(): void
    {
        $values = self::faker()->linkAlternateResponse();
        unset($values['path']);

        self::expectException(\InvalidArgumentException::class);

        new LinkAlternate($values);
    }

    /**
     * @test
     */
    public function published(): void
    {
        $values = self::faker()->linkAlternateResponse();

        self::assertSame($values['published'], (new LinkAlternate($values))->published);
    }

    /**
     * @test
     */
    public function publishedKeyMustExist(): void
    {
        $values = self::faker()->linkAlternateResponse();
        unset($values['published']);

        self::expectException(\InvalidArgumentException::class);

        new LinkAlternate($values);
    }

    /**
     * @test
     */
    public function slug(): void
    {
        $values = self::faker()->linkAlternateResponse();

        self::assertSame($values['translated_slug'], (new LinkAlternate($values))->slug);
    }

    /**
     * @test
     */
    public function slugKeyMustExist(): void
    {
        $values = self::faker()->linkAlternateResponse();
        unset($values['translated_slug']);

        self::expectException(\InvalidArgumentException::class);

        new LinkAlternate($values);
    }

    /**
     * @test
     */
    public function isPublishedReturnsTrue(): void
    {
        $values = self::faker()->linkAlternateResponse(['published' => true]);

        self::assertTrue((new LinkAlternate($values))->isPublished());
    }

    /**
     * @test
     */
    public function isPublishedReturnsFalse(): void
    {
        $values = self::faker()->linkAlternateResponse(['published' => false]);

        self::assertFalse((new LinkAlternate($values))->isPublished());
    }
}
