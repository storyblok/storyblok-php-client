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

use SensioLabs\Storyblok\Api\Response\AssetResponse;

/**
 * @author Silas Joisten <silas.joisten@proton.me>
 */
interface AssetsApiInterface
{
    public function get(string $fileName): AssetResponse;
}
