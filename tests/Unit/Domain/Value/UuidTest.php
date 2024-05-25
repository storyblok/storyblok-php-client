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
use SensioLabs\Storyblok\Api\Domain\Value\Uuid;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

final class UuidTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function value(): void
    {
        $value = self::faker()->uuid();

        self::assertSame($value, (new Uuid($value))->value);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank()
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty()
     * @dataProvider provideInvalidValues
     */
    public function valueInvalid(string $value): void
    {
        self::expectException(\InvalidArgumentException::class);

        new Uuid($value);
    }

    /**
     * @return iterable<array{0: string}>
     */
    public static function provideInvalidValues(): iterable
    {
        yield 'not_an_uuid' => ['Zc2c176d'];
        yield 'invalid_uuid' => ['Zc2c176d-d8a1-457c-b066-f37ab38771ad'];
        yield 'mixed_case_uuid' => ['9C2c176d-d8a1-457c-b066-f37ab38771ad'];
        yield 'upper_case_uuid' => ['9C2C176D-D8A1-457C-B066-F37AB38771AD'];
    }

    /**
     * @test
     */
    public function stringable(): void
    {
        $uuid = new Uuid($expected = self::faker()->uuid());

        self::assertSame($expected, (string) $uuid);
        self::assertSame($expected, $uuid->__toString());
    }
}
