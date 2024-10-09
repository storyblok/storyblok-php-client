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

use OskarStark\Value\TrimmedNonEmptyString;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SensioLabs\Storyblok\Api\Bridge\HttpClient\QueryStringHelper;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Webmozart\Assert\Assert;
use function Safe\parse_url;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class StoryblokClient implements StoryblokClientInterface
{
    private HttpClientInterface $client;
    private ?int $cacheVersion = null;

    public function __construct(
        string $baseUri,
        #[\SensitiveParameter]
        private string $token,
        int $timeout = 4,
        private LoggerInterface $logger = new NullLogger(),
    ) {
        $this->client = HttpClient::createForBaseUri($baseUri);
        $this->token = TrimmedNonEmptyString::fromString($token, '$token must not be an empty string')->toString();

        $this->client = HttpClient::createForBaseUri($baseUri, [
            'timeout' => $timeout,
        ]);
    }

    public function withHttpClient(HttpClientInterface $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        Assert::notStartsWith($url, 'http', '$url should be relative: Got: %s');
        Assert::startsWith($url, '/', '$url should start with a "/". Got: %s');

        /*
         * This workaround is necessary because the symfony/http-client does not support URL array syntax like in JavaScript.
         * Specifically, this issue arises with the "OrFilter" query parameter, which needs to be formatted as follows:
         * query_filter[__or][][field][filter]=value
         *
         * The default behavior of the Http Client includes the array key in the query string, causing a 500 error on the Storyblok API side.
         * Instead of generating the required format, the symfony/http-client generates a query string that looks like:
         * query_filter[__or][0][field][filter]=value&query_filter[__or][1][field][filter]=value
         */
        if (\array_key_exists('query', $options)) {
            $url = QueryStringHelper::applyQueryString($url, [
                ...$options['query'],
                'token' => $this->token,
                'cv' => $this->cacheVersion,
            ]);
            unset($options['query']);
        } else {
            $options['query'] = [
                'token' => $this->token,
                'cv' => $this->cacheVersion,
            ];
        }

        try {
            $response = $this->client->request(
                $method,
                $url,
                array_merge_recursive(
                    $options,
                    [
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                        ],
                    ],
                ),
            );

            $this->logger->debug('Response', $response->toArray(false));
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }

        if ($response->getStatusCode()) {
            $parsed = [];
            /** @var string $parsedUrl */
            $parsedUrl = parse_url($response->getInfo('url'), \PHP_URL_QUERY);
            parse_str($parsedUrl, $parsed);

            $this->cacheVersion = \array_key_exists('cv', $parsed) ? (int) $parsed['cv'] : $this->cacheVersion;
        }

        return $response;
    }
}
