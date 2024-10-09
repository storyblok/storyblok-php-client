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

namespace SensioLabs\Storyblok\Api\Bridge\HttpClient;

use Symfony\Component\HttpClient\HttpClientTrait;
use Webmozart\Assert\Assert;
use function Safe\preg_replace;

/**
 * Helper class to apply query string parameters to a URL.
 *
 *  This workaround is necessary because the symfony/http-client does not support URL array syntax like in JavaScript.
 *  Specifically, this issue arises with the "OrFilter" query parameter, which needs to be formatted as follows:
 *  query_filter[__or][][field][filter]=value
 *
 *  The default behavior of the Http Client includes the array key in the query string, causing a 500 error on the Storyblok API side.
 *  Instead of generating the required format, the symfony/http-client generates a query string that looks like:
 *  query_filter[__or][0][field][filter]=value&query_filter[__or][1][field][filter]=value
 *
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Simon André <smn.andre@gmail.com>
 */
final class QueryStringHelper
{
    use HttpClientTrait;

    /**
     * @param array<int|string, mixed> $parameters
     */
    public static function applyQueryString(string $url, array $parameters = []): string
    {
        if ([] === $parameters) {
            return $url;
        }

        $query = self::mergeQueryString('', $parameters, false);
        Assert::string($query);

        $query = preg_replace('/\[__or]\[(\d+)]/', '[__or][]', $query);

        if (str_contains($url, '?')) {
            $url .= '&'.$query;
        } else {
            $url .= '?'.$query;
        }

        return $url;
    }
}
