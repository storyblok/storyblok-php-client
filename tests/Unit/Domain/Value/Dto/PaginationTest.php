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

namespace SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value\Dto;

use PHPUnit\Framework\TestCase;
use SensioLabs\Storyblok\Api\Domain\Value\Dto\Pagination;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

final class PaginationTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function defaults(): void
    {
        self::assertSame(1, (new Pagination())->page);
        self::assertSame(25, (new Pagination())->perPage);
    }

    /**
     * @test
     */
    public function page(): void
    {
        $value = self::faker()->numberBetween(1);

        self::assertSame($value, (new Pagination($value))->page);
    }

    /**
     * @test
     */
    public function pageMustBePositiveInt(): void
    {
        $value = self::faker()->numberBetween(-100, 0);

        self::expectException(\InvalidArgumentException::class);

        new Pagination($value);
    }

    /**
     * @test
     */
    public function pageMustNotBeZero(): void
    {
        self::expectException(\InvalidArgumentException::class);

        new Pagination(0);
    }

    /**
     * @test
     */
    public function perPage(): void
    {
        $value = self::faker()->numberBetween(1);

        self::assertSame($value, (new Pagination(perPage: $value))->perPage);
    }

    /**
     * @test
     */
    public function perPageMustBePositiveInt(): void
    {
        $value = self::faker()->numberBetween(-100, 0);

        self::expectException(\InvalidArgumentException::class);

        new Pagination(perPage: $value);
    }

    /**
     * @test
     */
    public function perPageMustNotBeZero(): void
    {
        self::expectException(\InvalidArgumentException::class);

        new Pagination(perPage: 0);
    }
}
