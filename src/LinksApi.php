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

use SensioLabs\Storyblok\Api\Domain\Value\Id;
use SensioLabs\Storyblok\Api\Domain\Value\Total;
use SensioLabs\Storyblok\Api\Request\LinksRequest;
use SensioLabs\Storyblok\Api\Response\LinksResponse;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Simon André <smn.andre@gmail.com>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class LinksApi implements LinksApiInterface
{
    private const string ENDPOINT = '/v2/cdn/links';

    public function __construct(
        private StoryblokClientInterface $client,
    ) {
    }

    public function all(?LinksRequest $request = null): LinksResponse
    {
        $request ??= new LinksRequest();

        $response = $this->client->request('GET', self::ENDPOINT, [
            'query' => $request->toArray(),
        ]);

        return new LinksResponse(
            Total::fromHeaders($response->getHeaders()),
            $request->pagination,
            $response->toArray(),
        );
    }

    public function byParent(Id $parentId, ?LinksRequest $request = null): LinksResponse
    {
        $request ??= new LinksRequest();

        $response = $this->client->request('GET', self::ENDPOINT, [
            'query' => [
                ...$request->toArray(),
                'with_parent' => $parentId->value,
            ],
        ]);

        return new LinksResponse(
            Total::fromHeaders($response->getHeaders()),
            $request->pagination,
            $response->toArray(),
        );
    }

    public function roots(?LinksRequest $request = null): LinksResponse
    {
        $request ??= new LinksRequest();

        $response = $this->client->request('GET', self::ENDPOINT, [
            'query' => [
                ...$request->toArray(),
                'with_parent' => 0,
            ],
        ]);

        return new LinksResponse(
            Total::fromHeaders($response->getHeaders()),
            $request->pagination,
            $response->toArray(),
        );
    }
}
