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
use SensioLabs\Storyblok\Api\Response\DatasourceEntriesResponse;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Simon André <smn.andre@gmail.com>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class DatasourceEntriesResponseTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function datasourceEntries(): void
    {
        $values = self::faker()->datasourceEntriesResponse();

        self::assertCount(
            \count($values['datasource_entries']),
            (new DatasourceEntriesResponse(new Total(1), new Pagination(), $values))->datasourceEntries,
        );
    }

    /**
     * @test
     */
    public function datasourceEntriesKeyMustExist(): void
    {
        $values = self::faker()->datasourceEntriesResponse();
        unset($values['datasource_entries']);

        self::expectException(\InvalidArgumentException::class);

        new DatasourceEntriesResponse(new Total(1), new Pagination(), $values);
    }
}
