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

namespace SensioLabs\Storyblok\Api\Tests\Util;

use SensioLabs\Storyblok\Api\Bridge\Faker\Generator;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Simon André <smn.andre@gmail.com>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
trait FakerTrait
{
    final protected static function faker(string $locale = 'de_DE'): Generator
    {
        static $fakers = [];

        if (!\array_key_exists($locale, $fakers)) {
            $fakers[$locale] = new Generator();
        }

        return $fakers[$locale];
    }
}
