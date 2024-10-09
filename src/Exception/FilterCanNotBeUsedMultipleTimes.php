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

namespace SensioLabs\Storyblok\Api\Exception;

use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\Filter;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
class FilterCanNotBeUsedMultipleTimes extends \InvalidArgumentException
{
    public static function fromFilter(Filter $filter): self
    {
        return new self(\sprintf(
            'Filter "%s" can not be used multiple times',
            $filter::class,
        ));
    }
}
