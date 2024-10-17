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

namespace SensioLabs\Storyblok\Api\Domain\Value\Dto;

use OskarStark\Enum\Trait\Comparable;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 */
enum Version: string
{
    use Comparable;

    case Published = 'published';
    case Draft = 'draft';
}
