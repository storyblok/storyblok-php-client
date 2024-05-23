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

namespace SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters;

use SensioLabs\Storyblok\Api\Domain\Value\Filter\FilterCollection;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Operation;
use Webmozart\Assert\Assert;

final readonly class OrFilter extends Filter
{
    private FilterCollection $filters;

    public function __construct(Filter ...$filters)
    {
        Assert::isList($filters);
        $this->filters = new FilterCollection($filters);

        Assert::minCount($this->filters, 2);
    }

    public function toArray(): array
    {
        return [
            self::operation()->value => [
                ...array_map(static fn (Filter $filter): array => $filter->toArray(), [...$this->filters]),
            ],
        ];
    }

    public static function operation(): Operation
    {
        return Operation::Or;
    }
}
