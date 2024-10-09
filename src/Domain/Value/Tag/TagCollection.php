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

namespace SensioLabs\Storyblok\Api\Domain\Value\Tag;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Oskar Stark <oskarstark@googlemail.com>
 *
 * @implements \IteratorAggregate<int, Tag>
 */
final class TagCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var list<Tag>
     */
    private array $items = [];

    /**
     * @param list<Tag> $items
     */
    public function __construct(
        array $items = [],
    ) {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * @return \Traversable<int, Tag>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    public function count(): int
    {
        return \count($this->items);
    }

    public function add(Tag $field): void
    {
        if ($this->has($field)) {
            return;
        }

        $this->items[] = $field;
    }

    public function has(Tag $field): bool
    {
        foreach ($this->items as $item) {
            if ($item->value === $field->value) {
                return true;
            }
        }

        return false;
    }

    public function remove(Tag $field): void
    {
        foreach ($this->items as $key => $item) {
            if ($item->value === $field->value) {
                unset($this->items[$key]);

                break;
            }
        }
    }

    public function toString(): string
    {
        return implode(',', array_map(static fn (Tag $tag): string => $tag->value, $this->items));
    }
}
