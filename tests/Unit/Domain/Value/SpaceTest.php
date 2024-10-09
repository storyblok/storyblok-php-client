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
use SensioLabs\Storyblok\Api\Domain\Value\Space;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Simon André <smn.andre@gmail.com>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class SpaceTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function id(): void
    {
        $faker = self::faker();
        $response = $faker->spaceResponse([
            'id' => $id = $faker->numberBetween(1),
        ]);

        self::assertSame($id, (new Space($response))->id->value);
    }

    /**
     * @test
     */
    public function idKeyMustExist(): void
    {
        $faker = self::faker();
        $response = $faker->spaceResponse();
        unset($response['id']);

        self::expectException(\InvalidArgumentException::class);

        new Space($response);
    }

    /**
     * @test
     */
    public function name(): void
    {
        $faker = self::faker();
        $response = $faker->spaceResponse([
            'name' => $name = $faker->word(),
        ]);

        self::assertSame($name, (new Space($response))->name);
    }

    /**
     * @test
     */
    public function nameKeyMustExist(): void
    {
        $faker = self::faker();
        $response = $faker->spaceResponse();
        unset($response['name']);

        self::expectException(\InvalidArgumentException::class);

        new Space($response);
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
        $response = $faker->spaceResponse(['name' => $value]);

        self::expectException(\InvalidArgumentException::class);

        new Space($response);
    }

    /**
     * @test
     */
    public function version(): void
    {
        $faker = self::faker();
        $response = $faker->spaceResponse([
            'version' => $version = $faker->numberBetween(1),
        ]);

        self::assertSame($version, (new Space($response))->version);
    }

    /**
     * @test
     */
    public function versionKeyMustExist(): void
    {
        $faker = self::faker();
        $response = $faker->spaceResponse();
        unset($response['version']);

        self::expectException(\InvalidArgumentException::class);

        new Space($response);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\DataProvider\IntProvider::lessThanZero()
     * @dataProvider \Ergebnis\DataProvider\IntProvider::zero()
     * @dataProvider \Ergebnis\DataProvider\StringProvider::arbitrary()
     */
    public function versionInvalid(int|string $value): void
    {
        $faker = self::faker();
        $response = $faker->spaceResponse(['version' => $value]);

        self::expectException(\InvalidArgumentException::class);

        new Space($response);
    }

    /**
     * @test
     */
    public function languageCodes(): void
    {
        $faker = self::faker();
        $response = $faker->spaceResponse([
            'language_codes' => $languageCodes = ['de', 'en', 'fr'],
        ]);

        self::assertSame($languageCodes, (new Space($response))->languageCodes);
    }

    /**
     * @test
     */
    public function languageCodesKeyMustExist(): void
    {
        $faker = self::faker();
        $response = $faker->spaceResponse();
        unset($response['language_codes']);

        self::expectException(\InvalidArgumentException::class);

        new Space($response);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\DataProvider\IntProvider::arbitrary()
     * @dataProvider \Ergebnis\DataProvider\NullProvider::null()
     * @dataProvider \Ergebnis\DataProvider\StringProvider::arbitrary()
     */
    public function languageCodesMustBeArray(mixed $value): void
    {
        $faker = self::faker();
        $response = $faker->spaceResponse(['language_codes' => $value]);

        self::expectException(\InvalidArgumentException::class);

        new Space($response);
    }

    /**
     * @test
     */
    public function languageCodesMustAllArray(): void
    {
        $faker = self::faker();
        $response = $faker->spaceResponse(['language_codes' => ['de', $faker->randomNumber()]]);

        self::expectException(\InvalidArgumentException::class);

        new Space($response);
    }
}
