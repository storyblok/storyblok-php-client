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
use SensioLabs\Storyblok\Api\Domain\Value\DatasourceDimension;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Simon André <smn.andre@gmail.com>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class DatasourceDimensionTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function id(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceDimensionResponse([
            'id' => $id = $faker->numberBetween(1),
        ]);

        self::assertSame($id, (new DatasourceDimension($response))->id->value);
    }

    /**
     * @test
     */
    public function idKeyMustExist(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceDimensionResponse();
        unset($response['id']);

        self::expectException(\InvalidArgumentException::class);

        new DatasourceDimension($response);
    }

    /**
     * @test
     */
    public function name(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceDimensionResponse([
            'name' => $name = $faker->word(),
        ]);

        self::assertSame($name, (new DatasourceDimension($response))->name);
    }

    /**
     * @test
     */
    public function nameKeyMustExist(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceDimensionResponse();
        unset($response['name']);

        self::expectException(\InvalidArgumentException::class);

        new DatasourceDimension($response);
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
        $response = $faker->datasourceDimensionResponse(['name' => $value]);

        self::expectException(\InvalidArgumentException::class);

        new DatasourceDimension($response);
    }

    /**
     * @test
     */
    public function entryValue(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceDimensionResponse([
            'entry_value' => $entryValue = $faker->word(),
        ]);

        self::assertSame($entryValue, (new DatasourceDimension($response))->entryValue);
    }

    /**
     * @test
     */
    public function entryValueKeyMustExist(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceDimensionResponse();
        unset($response['entry_value']);

        self::expectException(\InvalidArgumentException::class);

        new DatasourceDimension($response);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank()
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty()
     */
    public function entryValueInvalid(string $value): void
    {
        $faker = self::faker();
        $response = $faker->datasourceDimensionResponse(['entry_value' => $value]);

        self::expectException(\InvalidArgumentException::class);

        new DatasourceDimension($response);
    }

    /**
     * @test
     */
    public function datasourceId(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceDimensionResponse([
            'datasource_id' => $id = $faker->numberBetween(1),
        ]);

        self::assertSame($id, (new DatasourceDimension($response))->datasourceId->value);
    }

    /**
     * @test
     */
    public function datasourceIdKeyMustExist(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceDimensionResponse();
        unset($response['datasource_id']);

        self::expectException(\InvalidArgumentException::class);

        new DatasourceDimension($response);
    }

    /**
     * @test
     */
    public function createdAt(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceDimensionResponse([
            'created_at' => $createdAt = $faker->dateTime()->format('Y-m-d H:i'),
        ]);

        self::assertSame($createdAt, (new DatasourceDimension($response))->createdAt->format('Y-m-d H:i'));
    }

    /**
     * @test
     */
    public function createdAtKeyMustExist(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceDimensionResponse();
        unset($response['created_at']);

        self::expectException(\InvalidArgumentException::class);

        new DatasourceDimension($response);
    }

    /**
     * @test
     */
    public function updatedAt(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceDimensionResponse([
            'updated_at' => $updatedAt = $faker->dateTime()->format('Y-m-d H:i'),
        ]);

        self::assertSame($updatedAt, (new DatasourceDimension($response))->updatedAt->format('Y-m-d H:i'));
    }

    /**
     * @test
     */
    public function updatedAtKeyMustExist(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceDimensionResponse();
        unset($response['updated_at']);

        self::expectException(\InvalidArgumentException::class);

        new DatasourceDimension($response);
    }
}
