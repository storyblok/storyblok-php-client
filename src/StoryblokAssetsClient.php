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

use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 */
final class StoryblokAssetsClient implements StoryblokClientInterface
{
    public function __construct(
        #[\SensitiveParameter]
        private string $token,
        private StoryblokClientInterface $client,
    ) {
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        return $this->client->request(
            $method,
            $url,
            array_replace_recursive($options, ['query' => ['token' => $this->token]]),
        );
    }
}
