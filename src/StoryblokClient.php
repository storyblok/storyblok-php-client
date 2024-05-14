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
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Webmozart\Assert\Assert;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 */
final class StoryblokClient implements StoryblokClientInterface
{
    private HttpClientInterface $client;
    private string $token;
    private int $timeout;

    public function __construct(
        string $baseUri,
        #[\SensitiveParameter]
        string $token,
        int $timeout = 4,
        ?HttpClientInterface $storyblokClient = null,
    ) {
        $this->client = $storyblokClient ?? HttpClient::createForBaseUri($baseUri);
        $this->token = TrimmedNonEmptyString::fromString($token, '$token must not be an empty string')->toString();
        $this->timeout = $timeout;
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        Assert::notStartsWith($url, 'http', '$url should be relative: Got: %s');
        Assert::startsWith($url, '/', '$url should start with a "/". Got: %s');

        if (!\array_key_exists('timeout', $options)) {
            $options['timeout'] = $this->timeout;
        }

        return $this->client->request(
            $method,
            $url,
            array_merge_recursive(
                $options,
                [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ],
                    'query' => [
                        'token' => $this->token,
                    ],
                ],
            ),
        );
    }
}
