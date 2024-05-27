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
use SensioLabs\Storyblok\Api\Response\DatasourceResponse;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

final class DatasourceResponseTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function datasource(): void
    {
        $faker = self::faker();
        $values = ['datasource' => $faker->datasourceResponse([
            'name' => $name = $faker->word(),
        ])];

        self::assertSame($name, (new DatasourceResponse($values))->datasource->name);
    }

    /**
     * @test
     */
    public function datasourceKeyMustExist(): void
    {
        $values = self::faker()->datasourceResponse();

        self::expectException(\InvalidArgumentException::class);

        new DatasourceResponse($values);
    }
}
