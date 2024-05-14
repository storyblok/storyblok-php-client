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

use SensioLabs\Storyblok\Api\Bridge\HttpClient\CacheableResponse;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Webmozart\Assert\Assert;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 */
final class CachingStoryblokClient implements StoryblokClientInterface
{
    private CacheInterface $cache;

    public function __construct(
        private StoryblokClientInterface $client,
        ?CacheInterface $cache = null,
    ) {
        $this->cache = $cache ?? new FilesystemAdapter(
            'storyblok-api',
            86400, // 1 day default lifetime
            sys_get_temp_dir(),
        );
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        Assert::notStartsWith($url, 'http', '$url should be relative: Got: %s');
        Assert::startsWith($url, '/', '$url should start with a "/". Got: %s');

        return $this->cache->get(
            hash('crc32', sprintf('%s-%s-%s', $method, $url, \json_encode($options, \JSON_THROW_ON_ERROR))),
            function () use ($method, $url, $options) {
                $response = $this->client->request($method, $url, $options);

                return new CacheableResponse($response);
            },
        );
    }
}
