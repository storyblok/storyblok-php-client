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

use SensioLabs\Storyblok\Api\Response\TagsResponse;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Simon André <smn.andre@gmail.com>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final readonly class TagsApi implements TagsApiInterface
{
    private const string ENDPOINT = '/v2/cdn/tags';

    public function __construct(
        private StoryblokClientInterface $client,
    ) {
    }

    public function all(): TagsResponse
    {
        $response = $this->client->request('GET', self::ENDPOINT);

        return new TagsResponse($response->toArray());
    }
}
