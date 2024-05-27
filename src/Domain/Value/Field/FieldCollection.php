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

namespace SensioLabs\Storyblok\Api\Domain\Value\Field;

/**
 * @implements \IteratorAggregate<int, Field>
 */
final class FieldCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var list<Field>
     */
    private array $items = [];

    /**
     * @param list<Field> $items
     */
    public function __construct(
        array $items = [],
    ) {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * @return \Traversable<int, Field>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    public function count(): int
    {
        return \count($this->items);
    }

    public function add(Field $field): void
    {
        if ($this->has($field)) {
            return;
        }

        $this->items[] = $field;
    }

    public function has(Field $field): bool
    {
        foreach ($this->items as $item) {
            if ($item->value === $field->value) {
                return true;
            }
        }

        return false;
    }

    public function remove(Field $field): void
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
        return implode(',', array_map(static fn (Field $field): string => $field->value, $this->items));
    }
}
