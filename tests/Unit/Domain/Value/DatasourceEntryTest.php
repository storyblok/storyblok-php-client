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
use SensioLabs\Storyblok\Api\Domain\Value\DatasourceEntry;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

final class DatasourceEntryTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function id(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceEntryResponse([
            'id' => $id = $faker->numberBetween(1),
        ]);

        self::assertSame($id, (new DatasourceEntry($response))->id->value);
    }

    /**
     * @test
     */
    public function idKeyMustExist(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceEntryResponse();
        unset($response['id']);

        self::expectException(\InvalidArgumentException::class);

        new DatasourceEntry($response);
    }

    /**
     * @test
     */
    public function name(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceEntryResponse([
            'name' => $name = $faker->word(),
        ]);

        self::assertSame($name, (new DatasourceEntry($response))->name);
    }

    /**
     * @test
     */
    public function nameKeyMustExist(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceEntryResponse();
        unset($response['name']);

        self::expectException(\InvalidArgumentException::class);

        new DatasourceEntry($response);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank()
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty()
     */
    public function nameInvalid(string $value): void
    {
        $faker = self::faker();
        $response = $faker->datasourceEntryResponse(['name' => $value]);

        self::expectException(\InvalidArgumentException::class);

        new DatasourceEntry($response);
    }

    /**
     * @test
     */
    public function value(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceEntryResponse([
            'value' => $value = $faker->word(),
        ]);

        self::assertSame($value, (new DatasourceEntry($response))->value);
    }

    /**
     * @test
     */
    public function valueKeyMustExist(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceEntryResponse();
        unset($response['value']);

        self::expectException(\InvalidArgumentException::class);

        new DatasourceEntry($response);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank()
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty()
     */
    public function valueInvalid(string $value): void
    {
        $faker = self::faker();
        $response = $faker->datasourceEntryResponse(['value' => $value]);

        self::expectException(\InvalidArgumentException::class);

        new DatasourceEntry($response);
    }

    /**
     * @test
     */
    public function dimensionValue(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceEntryResponse([
            'dimension_value' => $dimensionValue = $faker->word(),
        ]);

        self::assertSame($dimensionValue, (new DatasourceEntry($response))->dimensionValue);
    }

    /**
     * @test
     */
    public function dimensionValueCanBeNull(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceEntryResponse([
            'dimension_value' => null,
        ]);

        self::assertNull((new DatasourceEntry($response))->dimensionValue);
    }

    /**
     * @test
     */
    public function dimensionValueKeyMustExist(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceEntryResponse();
        unset($response['dimension_value']);

        self::expectException(\InvalidArgumentException::class);

        new DatasourceEntry($response);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank()
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty()
     */
    public function dimensionValueInvalid(string $value): void
    {
        $faker = self::faker();
        $response = $faker->datasourceEntryResponse(['dimension_value' => $value]);

        self::expectException(\InvalidArgumentException::class);

        new DatasourceEntry($response);
    }
}
