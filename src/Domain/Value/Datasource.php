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

use OskarStark\Value\TrimmedNonEmptyString;
use SensioLabs\Storyblok\Api\Domain\Value\Datasource\Dimension;
use SensioLabs\Storyblok\Api\Domain\Value\Datasource\Entry;
use Webmozart\Assert\Assert;

final readonly class Datasource
{
    /**
     * @var Entry[]
     */
    public array $entries;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(
        public string $name,
        public Dimension $dimension,
        array $values,
    ) {
        TrimmedNonEmptyString::fromString($name);

        Assert::keyExists($values, 'datasource_entries');
        Assert::isArray($values['datasource_entries']);

        $entries = [];

        if ([] !== $values['datasource_entries']) {
            foreach ($values['datasource_entries'] as $entry) {
                Assert::isArray($entry);
                $entries[] = new Entry($entry);
            }
        }

        $this->entries = $entries;
    }
}
