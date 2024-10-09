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
use SensioLabs\Storyblok\Api\Response\LinksResponse;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Simon André <smn.andre@gmail.com>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class LinksResponseTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function links(): void
    {
        $values = self::faker()->linksResponse();

        self::assertCount(
            \count($values['links']),
            (new LinksResponse(new Total(1), new Pagination(), $values))->links,
        );
    }

    /**
     * @test
     */
    public function linksKeyMustExist(): void
    {
        self::expectException(\InvalidArgumentException::class);

        new LinksResponse(new Total(1), new Pagination(), []);
    }
}
