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
use SensioLabs\Storyblok\Api\Domain\Value\Total;
use SensioLabs\Storyblok\Api\Response\LinksResponse;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

final class LinksResponseTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function links(): void
    {
        $values = self::faker()->linksResponse();

        self::assertCount(\count($values['links']), (new LinksResponse(new Total(1), $values))->links);
    }

    /**
     * @test
     */
    public function linksKeyMustExist(): void
    {
        self::expectException(\InvalidArgumentException::class);

        new LinksResponse(new Total(1), []);
    }
}
