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

namespace SensioLabs\Storyblok\Api\Tests\Unit\Bridge\HttpClient;

use PHPUnit\Framework\TestCase;
use SensioLabs\Storyblok\Api\Bridge\HttpClient\QueryStringHelper;

class QueryStringHelperTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider provideTestCases
     *
     * @param array<int|string, mixed> $parameters
     */
    public function itAppliesQueryString(string $expected, string $url, array $parameters): void
    {
        self::assertSame($expected, QueryStringHelper::applyQueryString($url, $parameters));
    }

    /**
     * @return iterable<array{string, string, array<int|string, mixed>}>
     */
    public static function provideTestCases(): iterable
    {
        yield 'Empty parameters' => [
            'https://example.com',
            'https://example.com',
            [],
        ];

        yield 'Apply parameters' => [
            'http://example.com?param1=value1&param2=value2',
            'http://example.com',
            ['param1' => 'value1', 'param2' => 'value2'],
        ];

        yield 'Url containing query string' => [
            'http://example.com?existingParam=existingValue&param1=value1&param2=value2',
            'http://example.com?existingParam=existingValue',
            ['param1' => 'value1', 'param2' => 'value2'],
        ];

        yield 'Or filter parameters' => [
            'http://example.com?query_filter[__or][][field][filter]=value',
            'http://example.com',
            ['query_filter' => ['__or' => [['field' => ['filter' => 'value']]]]],
        ];

        yield 'Or filter parameters with multiple values' => [
            'http://example.com?query_filter[__or][][field][filter]=value1&query_filter[__or][][field][filter]=value2',
            'http://example.com',
            [
                'query_filter' => ['__or' => [
                    ['field' => ['filter' => 'value1']],
                    ['field' => ['filter' => 'value2']],
                ]],
            ],
        ];
    }
}
