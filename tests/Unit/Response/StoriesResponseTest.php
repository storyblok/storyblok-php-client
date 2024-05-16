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
use SensioLabs\Storyblok\Api\Domain\Value\Dto\Pagination;
use SensioLabs\Storyblok\Api\Domain\Value\Total;
use SensioLabs\Storyblok\Api\Response\StoriesResponse;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

final class StoriesResponseTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function stories(): void
    {
        $values = self::faker()->storiesResponse();

        self::assertCount(
            \count($values['stories']),
            (new StoriesResponse(new Total(1), new Pagination(), $values))->stories,
        );
    }

    /**
     * @test
     */
    public function storiesKeyMustExist(): void
    {
        $values = self::faker()->storiesResponse();
        unset($values['stories']);

        self::expectException(\InvalidArgumentException::class);

        new StoriesResponse(new Total(1), new Pagination(), $values);
    }

    /**
     * @test
     */
    public function cv(): void
    {
        $values = self::faker()->storiesResponse();

        self::assertSame(
            $values['cv'],
            (new StoriesResponse(new Total(1), new Pagination(), $values))->cv,
        );
    }

    /**
     * @test
     */
    public function cvKeyMustExist(): void
    {
        $values = self::faker()->storiesResponse();
        unset($values['cv']);

        self::expectException(\InvalidArgumentException::class);

        new StoriesResponse(new Total(1), new Pagination(), $values);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\DataProvider\StringProvider::arbitrary()
     */
    public function cvInvalid(string $value): void
    {
        $values = self::faker()->storiesResponse([
            'cv' => $value,
        ]);

        self::expectException(\InvalidArgumentException::class);
        new StoriesResponse(new Total(1), new Pagination(), $values);
    }

    /**
     * @test
     */
    public function rels(): void
    {
        $values = self::faker()->storiesResponse();

        self::assertCount(
            \count($values['rels']),
            (new StoriesResponse(new Total(1), new Pagination(), $values))->rels,
        );
    }

    /**
     * @test
     */
    public function relsKeyMustExist(): void
    {
        $values = self::faker()->storiesResponse();
        unset($values['rels']);

        self::expectException(\InvalidArgumentException::class);

        new StoriesResponse(new Total(1), new Pagination(), $values);
    }

    /**
     * @test
     */
    public function links(): void
    {
        $values = self::faker()->storiesResponse();

        self::assertCount(
            \count($values['links']),
            (new StoriesResponse(new Total(1), new Pagination(), $values))->links,
        );
    }

    /**
     * @test
     */
    public function linksKeyMustExist(): void
    {
        $values = self::faker()->storiesResponse();
        unset($values['links']);

        self::expectException(\InvalidArgumentException::class);

        new StoriesResponse(new Total(1), new Pagination(), $values);
    }
}
