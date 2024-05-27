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
use SensioLabs\Storyblok\Api\Domain\Value\Dto\Pagination;
use SensioLabs\Storyblok\Api\Domain\Value\Total;
use SensioLabs\Storyblok\Api\Response\DatasourcesResponse;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

final class DatasourcesResponseTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function datasources(): void
    {
        $values = self::faker()->datasourcesResponse();

        self::assertCount(
            \count($values['datasources']),
            (new DatasourcesResponse(new Total(1), new Pagination(), $values))->datasources,
        );
    }

    /**
     * @test
     */
    public function datasourcesKeyMustExist(): void
    {
        $values = self::faker()->datasourcesResponse();
        unset($values['datasources']);

        self::expectException(\InvalidArgumentException::class);

        new DatasourcesResponse(new Total(1), new Pagination(), $values);
    }
}
