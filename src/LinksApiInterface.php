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
use SensioLabs\Storyblok\Api\Response\LinksResponse;

/**
 * @author Silas Joisten <silas.joisten@proton.me>
 */
interface LinksApiInterface
{
    /**
     * @param array{
     *     'starts_with'?: string,
     *     'version'?: string,
     *     'cv'?: non-negative-int,
     *     'with_parent'?: boolean,
     *     'include_dates'?: boolean,
     *     'page'?: non-negative-int,
     *     'per_page'?: non-negative-int
     * } $parameter
     */
    public function all(array $parameter = []): LinksResponse;

    public function byParent(Id $parentId): LinksResponse;

    public function roots(): LinksResponse;
}
