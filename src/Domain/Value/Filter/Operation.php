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

namespace SensioLabs\Storyblok\Api\Domain\Value\Filter;

/**
 * @see https://www.storyblok.com/docs/api/content-delivery/v2/filter-queries
 */
enum Operation: string
{
    case Or = '__or';
    case Is = 'is';
    case In = 'in';
    case NotIn = 'not_in';
    case Like = 'like';
    case NotLike = 'not_like';
    case AnyInArray = 'any_in_array';
    case AllInArray = 'all_in_array';
    case GreaterThanDate = 'gt_date';
    case LessThanDate = 'lt_date';
    case GreaterThanInt = 'gt_int';
    case LessThanInt = 'lt_int';
    case GreaterThanFloat = 'gt_float';
    case LessThanFloat = 'lt_float';
}
