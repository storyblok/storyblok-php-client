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

use OskarStark\Value\TrimmedNonEmptyString;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Operation;
use Webmozart\Assert\Assert;

final readonly class AllInArrayFilter extends Filter
{
    /**
     * @param string[] $value
     */
    public function __construct(
        private string $field,
        private array $value,
    ) {
        TrimmedNonEmptyString::fromString($field);
        Assert::minCount($value, 1);
        Assert::allString($value);
        Assert::allStringNotEmpty($value);
        Assert::allNotWhitespaceOnly($value);
    }

    public function field(): string
    {
        return $this->field;
    }

    public function value(): string
    {
        return implode(',', $this->value);
    }

    public static function operation(): Operation
    {
        return Operation::AllInArray;
    }
}
