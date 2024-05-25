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
use SensioLabs\Storyblok\Api\Domain\Value\Datasource\Dimension;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

final class DatasourceTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function entries(): void
    {
        $faker = self::faker();

        $datasource = new Datasource(
            $name = $faker->word(),
            $dimension = new Dimension($faker->word()),
            $response = $faker->datasourceResponse(),
        );

        self::assertSame($datasource->name, $name);
        self::assertTrue($datasource->dimension->equals($dimension));
        self::assertCount(\count($response['datasource_entries']), $datasource->entries);
    }
}
