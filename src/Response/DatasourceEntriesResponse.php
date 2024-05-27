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

use SensioLabs\Storyblok\Api\Domain\Value\DatasourceEntry;
use SensioLabs\Storyblok\Api\Domain\Value\Dto\Pagination;
use SensioLabs\Storyblok\Api\Domain\Value\Total;
use Webmozart\Assert\Assert;

final readonly class DatasourceEntriesResponse
{
    /**
     * @var list<DatasourceEntry>
     */
    public array $datasourceEntries;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(
        public Total $total,
        public Pagination $pagination,
        array $values,
    ) {
        Assert::keyExists($values, 'datasource_entries');
        $this->datasourceEntries = array_map(
            static fn (array $entry): DatasourceEntry => new DatasourceEntry($entry),
            $values['datasource_entries'],
        );
    }
}
