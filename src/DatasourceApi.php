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
use SensioLabs\Storyblok\Api\Domain\Value\Datasource;
use SensioLabs\Storyblok\Api\Domain\Value\Datasource\Dimension;

final class DatasourceApi implements DatasourceApiInterface
{
    public function __construct(
        private StoryblokClientInterface $client,
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function byName(string $name, ?Dimension $dimension = null): Datasource
    {
        if (null === $dimension) {
            $dimension = new Dimension(null);
        }

        try {
            $response = $this->client->request(
                'GET',
                '/v2/cdn/datasource_entries',
                [
                    'query' => [
                        'datasource' => $name = TrimmedNonEmptyString::fromString($name)->toString(),
                        'dimension' => $dimension->value,
                    ],
                ],
            );

            $this->logger->debug('Response', $response->toArray(false));

            return new Datasource(
                $name,
                $dimension,
                $response->toArray(),
            );
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }
}
