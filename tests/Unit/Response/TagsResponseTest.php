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
use SensioLabs\Storyblok\Api\Response\TagsResponse;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Simon André <smn.andre@gmail.com>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class TagsResponseTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function tags(): void
    {
        $values = self::faker()->tagsResponse();

        self::assertCount(
            \count($values['tags']),
            (new TagsResponse($values))->tags,
        );
    }

    /**
     * @test
     */
    public function tagsKeyMustExist(): void
    {
        $values = self::faker()->tagsResponse();
        unset($values['tags']);

        self::expectException(\InvalidArgumentException::class);

        new TagsResponse($values);
    }
}
