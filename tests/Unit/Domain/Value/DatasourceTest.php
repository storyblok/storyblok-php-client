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
use SensioLabs\Storyblok\Api\Domain\Value\Datasource;
use SensioLabs\Storyblok\Api\Domain\Value\DatasourceDimension;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

final class DatasourceTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function id(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceResponse([
            'id' => $id = $faker->numberBetween(1),
        ]);

        self::assertSame($id, (new Datasource($response))->id->value);
    }

    /**
     * @test
     */
    public function idKeyMustExist(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceResponse();
        unset($response['id']);

        self::expectException(\InvalidArgumentException::class);

        new Datasource($response);
    }

    /**
     * @test
     */
    public function name(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceResponse([
            'name' => $name = $faker->word(),
        ]);

        self::assertSame($name, (new Datasource($response))->name);
    }

    /**
     * @test
     */
    public function nameKeyMustExist(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceResponse();
        unset($response['name']);

        self::expectException(\InvalidArgumentException::class);

        new Datasource($response);
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
        $response = $faker->datasourceResponse(['name' => $value]);

        self::expectException(\InvalidArgumentException::class);

        new Datasource($response);
    }

    /**
     * @test
     */
    public function slug(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceResponse([
            'slug' => $slug = $faker->word(),
        ]);

        self::assertSame($slug, (new Datasource($response))->slug);
    }

    /**
     * @test
     */
    public function slugKeyMustExist(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceResponse();
        unset($response['slug']);

        self::expectException(\InvalidArgumentException::class);

        new Datasource($response);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank()
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty()
     */
    public function slugInvalid(string $value): void
    {
        $faker = self::faker();
        $response = $faker->datasourceResponse(['slug' => $value]);

        self::expectException(\InvalidArgumentException::class);

        new Datasource($response);
    }

    /**
     * @test
     */
    public function dimensions(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceResponse();

        self::assertContainsOnlyInstancesOf(
            DatasourceDimension::class,
            (new Datasource($response))->dimensions,
        );
    }

    /**
     * @test
     */
    public function dimensionsKeyMustExist(): void
    {
        $faker = self::faker();
        $response = $faker->datasourceResponse();
        unset($response['dimensions']);

        self::expectException(\InvalidArgumentException::class);

        new Datasource($response);
    }
}
