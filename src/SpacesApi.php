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

use SensioLabs\Storyblok\Api\Response\SpaceResponse;

final readonly class SpacesApi implements SpacesApiInterface
{
    private const string ENDPOINT = '/v2/cdn/spaces';

    public function __construct(
        private StoryblokClientInterface $client,
    ) {
    }

    public function me(): SpaceResponse
    {
        $response = $this->client->request('GET', self::ENDPOINT.'/me');

        return new SpaceResponse($response->toArray());
    }
}
