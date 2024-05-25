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

use Symfony\Contracts\HttpClient\ResponseInterface;

final class CacheableResponse implements ResponseInterface
{
    private int $statusCode;

    /**
     * @var string[][]
     */
    private array $headers;
    private string $content;

    public function __construct(ResponseInterface $response)
    {
        $this->statusCode = $response->getStatusCode();
        $this->headers = $response->getHeaders();
        $this->content = $response->getContent();
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(bool $throw = true): array
    {
        return $this->headers;
    }

    public function getContent(bool $throw = true): string
    {
        return $this->content;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(bool $throw = true): array
    {
        if (!json_validate($this->content) && $throw) {
            throw new \RuntimeException('Content is no JSON.');
        }

        return json_decode($this->content, true);
    }

    public function cancel(): never
    {
        throw new \BadMethodCallException(sprintf('%s is not callable.', __METHOD__));
    }

    public function getInfo(?string $type = null): never
    {
        throw new \BadMethodCallException(sprintf('%s is not callable.', __METHOD__));
    }
}
