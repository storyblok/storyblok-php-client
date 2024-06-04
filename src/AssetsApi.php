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

use SensioLabs\Storyblok\Api\Response\AssetResponse;

final class AssetsApi implements AssetsApiInterface
{
    private const string ENDPOINT = '/v2/cdn/assets/me';

    public function __construct(
        private StoryblokClientInterface $client,
    ) {
    }

    public function get(string $fileName): AssetResponse
    {
        $response = $this->client->request('GET', self::ENDPOINT, [
            'query' => [
                'filename' => $fileName,
            ],
        ]);

        return new AssetResponse($response->toArray());
    }
}
