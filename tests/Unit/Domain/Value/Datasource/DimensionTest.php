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

namespace SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value\Datasource;

use PHPUnit\Framework\TestCase;
use SensioLabs\Storyblok\Api\Domain\Value\Datasource\Dimension;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

final class DimensionTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function value(): void
    {
        $value = self::faker()->word();

        self::assertSame($value, (new Dimension($value))->value);
    }

    /**
     * @test
     */
    public function valueCanBeNull(): void
    {
        self::assertSame('default', (new Dimension())->value);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank()
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty()
     */
    public function valueInvalid(string $value): void
    {
        self::expectException(\InvalidArgumentException::class);

        new Dimension($value);
    }

    /**
     * @test
     */
    public function equalsReturnsTrue(): void
    {
        $value = self::faker()->word();

        self::assertTrue((new Dimension($value))->equals(new Dimension($value)));
    }

    /**
     * @test
     */
    public function equalsReturnsFalse(): void
    {
        self::assertFalse((new Dimension(self::faker()->word()))->equals(new Dimension()));
    }
}
