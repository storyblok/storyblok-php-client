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

namespace SensioLabs\Storyblok\Api\Response;

use SensioLabs\Storyblok\Api\Domain\Value\Asset;
use Webmozart\Assert\Assert;

final readonly class AssetResponse
{
    public Asset $asset;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(
        array $values,
    ) {
        Assert::keyExists($values, 'asset');
        $this->asset = new Asset($values['asset']);
    }
}
