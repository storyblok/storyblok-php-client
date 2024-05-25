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
use SensioLabs\Storyblok\Api\Domain\Value\Datasource\Entry;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

final class EntryTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function id(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceEntryResponse(['id' => $id = $faker->unique()->randomNumber()]);

        self::assertSame($id, (new Entry($response))->id->value);
    }

    /**
     * @test
     */
    public function idKeyMustExist(): void
    {
        $response = self::faker()->datasourceEntryResponse();
        unset($response['id']);

        self::expectException(\InvalidArgumentException::class);

        new Entry($response);
    }

    /**
     * @test
     */
    public function idKeyMustBeInteger(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceEntryResponse(['id' => $faker->word()]);

        self::expectException(\InvalidArgumentException::class);

        new Entry($response);
    }

    /**
     * @test
     */
    public function name(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceEntryResponse(['name' => $name = $faker->word()]);

        self::assertSame($name, (new Entry($response))->name);
    }

    /**
     * @test
     */
    public function nameKeyMustExist(): void
    {
        $response = self::faker()->datasourceEntryResponse();
        unset($response['name']);

        self::expectException(\InvalidArgumentException::class);

        new Entry($response);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank()
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty()
     */
    public function nameInvalid(string $name): void
    {
        $response = self::faker()->datasourceEntryResponse(['name' => $name]);

        self::expectException(\InvalidArgumentException::class);

        new Entry($response);
    }

    /**
     * @test
     */
    public function value(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceEntryResponse(['value' => $value = $faker->word()]);

        self::assertSame($value, (new Entry($response))->value);
    }

    /**
     * @test
     */
    public function valueKeyMustExist(): void
    {
        $response = self::faker()->datasourceEntryResponse();
        unset($response['value']);

        self::expectException(\InvalidArgumentException::class);

        new Entry($response);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank()
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty()
     */
    public function valueInvalid(string $value): void
    {
        $response = self::faker()->datasourceEntryResponse(['value' => $value]);

        self::expectException(\InvalidArgumentException::class);

        new Entry($response);
    }

    /**
     * @test
     */
    public function dimensionValue(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceEntryResponse(['dimension_value' => $value = $faker->word()]);

        self::assertSame($value, (new Entry($response))->dimensionValue);
    }

    /**
     * @test
     */
    public function dimensionValueCanBeNull(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceEntryResponse(['dimension_value' => null]);

        self::assertNull((new Entry($response))->dimensionValue);
    }

    /**
     * @test
     */
    public function dimensionValueMustExist(): void
    {
        $response = self::faker()->datasourceEntryResponse();
        unset($response['dimension_value']);

        self::expectException(\InvalidArgumentException::class);

        new Entry($response);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\DataProvider\IntProvider::arbitrary()
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank()
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty()
     */
    public function dimensionValueInvalid(int|string $value): void
    {
        $response = self::faker()->datasourceEntryResponse(['dimension_value' => $value]);

        self::expectException(\InvalidArgumentException::class);

        new Entry($response);
    }
}
