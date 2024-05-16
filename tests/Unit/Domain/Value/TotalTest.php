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
use SensioLabs\Storyblok\Api\Domain\Value\Link;
use SensioLabs\Storyblok\Api\Domain\Value\Total;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

final class TotalTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function fromHeaders(): void
    {
        $headers = ['total' => ['5']];

        self::assertSame((int) $headers['total'][0], Total::fromHeaders($headers)->value);
    }

    /**
     * @test
     */
    public function totalKeyMustExist(): void
    {
        self::expectException(\InvalidArgumentException::class);

        new Link([]);
    }

    /**
     * @test
     */
    public function totalKeyMustContainExactlyOneItem(): void
    {
        self::expectException(\InvalidArgumentException::class);

        new Link(['total' => []]);
    }
}
