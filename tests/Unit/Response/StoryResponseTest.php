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

namespace SensioLabs\Storyblok\Api\Tests\Unit\Response;

use PHPUnit\Framework\TestCase;
use SensioLabs\Storyblok\Api\Response\StoryResponse;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class StoryResponseTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function storyKeyMustExist(): void
    {
        $values = self::faker()->storyResponse();
        unset($values['story']);

        self::expectException(\InvalidArgumentException::class);

        new StoryResponse($values);
    }

    /**
     * @test
     */
    public function cv(): void
    {
        $values = self::faker()->storyResponse();

        self::assertSame($values['cv'], (new StoryResponse($values))->cv);
    }

    /**
     * @test
     */
    public function cvKeyMustExist(): void
    {
        $values = self::faker()->storyResponse();
        unset($values['cv']);

        self::expectException(\InvalidArgumentException::class);

        new StoryResponse($values);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\DataProvider\StringProvider::arbitrary()
     */
    public function cvInvalid(string $value): void
    {
        $values = self::faker()->storyResponse([
            'cv' => $value,
        ]);

        self::expectException(\InvalidArgumentException::class);
        new StoryResponse($values);
    }

    /**
     * @test
     */
    public function rels(): void
    {
        $values = self::faker()->storyResponse();

        self::assertCount(\count($values['rels']), (new StoryResponse($values))->rels);
    }

    /**
     * @test
     */
    public function relsKeyMustExist(): void
    {
        $values = self::faker()->storyResponse();
        unset($values['rels']);

        self::expectException(\InvalidArgumentException::class);

        new StoryResponse($values);
    }

    /**
     * @test
     */
    public function links(): void
    {
        $values = self::faker()->storyResponse();

        self::assertCount(\count($values['links']), (new StoryResponse($values))->links);
    }

    /**
     * @test
     */
    public function linksKeyMustExist(): void
    {
        $values = self::faker()->storyResponse();
        unset($values['links']);

        self::expectException(\InvalidArgumentException::class);

        new StoryResponse($values);
    }
}
