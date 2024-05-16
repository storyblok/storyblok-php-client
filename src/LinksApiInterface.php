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

use SensioLabs\Storyblok\Api\Domain\Value\Dto\Pagination;
use SensioLabs\Storyblok\Api\Domain\Value\Id;
use SensioLabs\Storyblok\Api\Response\LinksResponse;

/**
 * @author Silas Joisten <silas.joisten@proton.me>
 */
interface LinksApiInterface
{
    public const int MAX_PER_PAGE = 1000;

    public function all(?Pagination $pagination = null): LinksResponse;

    public function byParent(Id $parentId, ?Pagination $pagination = null): LinksResponse;

    public function roots(?Pagination $pagination = null): LinksResponse;
}
