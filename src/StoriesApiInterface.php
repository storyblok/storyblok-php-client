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

namespace SensioLabs\Storyblok\Api;

use SensioLabs\Storyblok\Api\Domain\Value\Id;
use SensioLabs\Storyblok\Api\Domain\Value\Uuid;
use SensioLabs\Storyblok\Api\Request\StoriesRequest;
use SensioLabs\Storyblok\Api\Response\StoriesResponse;
use SensioLabs\Storyblok\Api\Response\StoryResponse;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
interface StoriesApiInterface
{
    public const int MAX_PER_PAGE = 100;

    public function all(?StoriesRequest $request = null): StoriesResponse;

    public function allByContentType(string $contentType, ?StoriesRequest $request = null): StoriesResponse;

    public function bySlug(string $slug, string $language = 'default'): StoryResponse;

    public function byUuid(Uuid $uuid, string $language = 'default'): StoryResponse;

    public function byId(Id $id, string $language = 'default'): StoryResponse;
}
