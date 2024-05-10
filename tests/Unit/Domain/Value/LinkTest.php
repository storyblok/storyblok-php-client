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
use SensioLabs\Storyblok\Api\Domain\Value\Link;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

final class LinkTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function uuid(): void
    {
        $values = self::faker()->linkResponse();

        self::assertSame($values['uuid'], (new Link($values))->uuid->value);
    }

    /**
     * @test
     */
    public function uuidKeyMustExist(): void
    {
        $values = self::faker()->linkResponse();
        unset($values['uuid']);

        self::expectException(\InvalidArgumentException::class);

        new Link($values);
    }

    /**
     * @test
     */
    public function id(): void
    {
        $values = self::faker()->linkResponse();

        self::assertSame($values['id'], (new Link($values))->id->value);
    }

    /**
     * @test
     */
    public function idKeyMustExist(): void
    {
        $values = self::faker()->linkResponse();
        unset($values['id']);

        self::expectException(\InvalidArgumentException::class);

        new Link($values);
    }

    /**
     * @test
     */
    public function parentId(): void
    {
        $values = self::faker()->linkResponse([
            'parent_id' => 24,
        ]);

        self::assertNotNull((new Link($values))->parentId);
        self::assertSame(24, (new Link($values))->parentId->value);
    }

    /**
     * @test
     */
    public function parentIdNull(): void
    {
        $values = self::faker()->linkResponse([
            'parent_id' => null,
        ]);

        self::assertNull((new Link($values))->parentId);
    }

    /**
     * @test
     */
    public function parentIdKeyMustExist(): void
    {
        $values = self::faker()->linkResponse();
        unset($values['parent_id']);

        self::expectException(\InvalidArgumentException::class);

        new Link($values);
    }

    /**
     * @test
     */
    public function position(): void
    {
        $values = self::faker()->linkResponse();

        self::assertSame($values['position'], (new Link($values))->position);
    }

    /**
     * @test
     */
    public function positionKeyMustExist(): void
    {
        $values = self::faker()->linkResponse();
        unset($values['position']);

        self::expectException(\InvalidArgumentException::class);

        new Link($values);
    }

    /**
     * @test
     */
    public function slug(): void
    {
        $values = self::faker()->linkResponse();

        self::assertSame($values['slug'], (new Link($values))->slug);
    }

    /**
     * @test
     */
    public function slugKeyMustExist(): void
    {
        $values = self::faker()->linkResponse();
        unset($values['slug']);

        self::expectException(\InvalidArgumentException::class);

        new Link($values);
    }

    /**
     * @test
     */
    public function path(): void
    {
        $values = self::faker()->linkResponse();

        self::assertSame($values['path'], (new Link($values))->path);
    }

    /**
     * @test
     */
    public function pathKeyMustExist(): void
    {
        $values = self::faker()->linkResponse();
        unset($values['path']);

        self::expectException(\InvalidArgumentException::class);

        new Link($values);
    }

    /**
     * @test
     */
    public function isFolder(): void
    {
        $values = self::faker()->linkResponse();

        self::assertSame($values['is_folder'], (new Link($values))->isFolder);
    }

    /**
     * @test
     */
    public function isFolderKeyMustExist(): void
    {
        $values = self::faker()->linkResponse();
        unset($values['is_folder']);

        self::expectException(\InvalidArgumentException::class);

        new Link($values);
    }

    /**
     * @test
     */
    public function isStartPage(): void
    {
        $values = self::faker()->linkResponse();

        self::assertSame($values['is_startpage'], (new Link($values))->isStartPage);
    }

    /**
     * @test
     */
    public function isStartPageKeyMustExist(): void
    {
        $values = self::faker()->linkResponse();
        unset($values['is_startpage']);

        self::expectException(\InvalidArgumentException::class);

        new Link($values);
    }

    /**
     * @test
     */
    public function isPublished(): void
    {
        $values = self::faker()->linkResponse();

        self::assertSame($values['published'], (new Link($values))->isPublished);
    }

    /**
     * @test
     */
    public function isPublishedNull(): void
    {
        $values = self::faker()->linkResponse();
        $values['published'] = null;

        self::assertFalse((new Link($values))->isPublished);
    }

    /**
     * @test
     */
    public function isPublishedKeyMustExist(): void
    {
        $values = self::faker()->linkResponse();
        unset($values['published']);

        self::expectException(\InvalidArgumentException::class);

        new Link($values);
    }

    /**
     * @test
     */
    public function realPath(): void
    {
        $values = self::faker()->linkResponse();

        self::assertSame($values['real_path'], (new Link($values))->realPath);
    }

    /**
     * @test
     */
    public function realPathKeyMustExist(): void
    {
        $values = self::faker()->linkResponse();
        unset($values['real_path']);

        self::expectException(\InvalidArgumentException::class);

        new Link($values);
    }
}
