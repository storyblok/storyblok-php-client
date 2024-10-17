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

namespace SensioLabs\Storyblok\Api\Domain\Value\Filter;

use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\Filter;
use SensioLabs\Storyblok\Api\Exception\FilterCanNotBeUsedMultipleTimes;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 *
 * @implements \IteratorAggregate<int, Filter>
 */
final class FilterCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var list<Filter>
     */
    private array $items = [];

    /**
     * @param list<Filter> $items
     */
    public function __construct(
        array $items = [],
    ) {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * @return \Traversable<int, Filter>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    public function count(): int
    {
        return \count($this->items);
    }

    public function add(Filter $filter): void
    {
        if ($this->has($filter)) {
            throw FilterCanNotBeUsedMultipleTimes::fromFilter($filter);
        }

        $this->items[] = $filter;
    }

    public function has(Filter $filter): bool
    {
        foreach ($this->items as $item) {
            if ($item->equals($filter)
                || ($item::class === $filter::class && $filter->field() === $item->field())
            ) {
                return true;
            }
        }

        return false;
    }

    public function remove(Filter $filter): void
    {
        foreach ($this->items as $key => $item) {
            if ($item->equals($filter)) {
                unset($this->items[$key]);

                break;
            }
        }
    }

    /**
     * @return list<string, mixed>
     */
    public function toArray(): array
    {
        return array_merge_recursive(...array_map(
            static fn (Filter $filter): array => $filter->toArray(),
            $this->items,
        ));
    }
}
