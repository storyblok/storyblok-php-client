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

namespace SensioLabs\Storyblok\Api\Domain\Value;

use Safe\DateTimeImmutable;
use Webmozart\Assert\Assert;

final readonly class Asset
{
    public string $filename;
    public \DateTimeImmutable $createdAt;
    public \DateTimeImmutable $updatedAt;
    public ?\DateTimeImmutable $expiresAt;
    public int $contentLength;
    public string $signedUrl;
    public string $contentType;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(
        array $values,
    ) {
        Assert::keyExists($values, 'filename');
        $this->filename = $values['filename'];

        Assert::keyExists($values, 'created_at');
        $this->createdAt = new DateTimeImmutable($values['created_at']);

        Assert::keyExists($values, 'updated_at');
        $this->updatedAt = new DateTimeImmutable($values['updated_at']);

        Assert::keyExists($values, 'expire_at');
        $expiresAt = null;

        if (null !== $values['expire_at']) {
            $expiresAt = new DateTimeImmutable($values['expire_at']);
        }

        $this->expiresAt = $expiresAt;

        Assert::keyExists($values, 'content_length');
        Assert::greaterThan($values['content_length'], 0);
        $this->contentLength = $values['content_length'];

        Assert::keyExists($values, 'signed_url');
        $this->signedUrl = $values['signed_url'];

        Assert::keyExists($values, 'content_type');
        $this->contentType = $values['content_type'];
    }
}
