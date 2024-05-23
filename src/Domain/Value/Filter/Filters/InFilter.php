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

final readonly class InFilter extends Filter
{
    /**
     * @var string[]
     */
    private array $value;

    /**
     * @param string|string[] $value
     */
    public function __construct(
        private string $field,
        array|string $value,
    ) {
        TrimmedNonEmptyString::fromString($field);

        $formattedValue = $value;

        if (\is_string($value)) {
            $formattedValue = [TrimmedNonEmptyString::fromString($value)->toString()];
        }

        Assert::isArray($formattedValue);
        Assert::minCount($formattedValue, 1);
        Assert::allString($formattedValue);
        Assert::allStringNotEmpty($formattedValue);
        Assert::allNotWhitespaceOnly($formattedValue);
        $this->value = $formattedValue;
    }

    public function toArray(): array
    {
        return [
            $this->field => [
                self::operation()->value => implode(',', $this->value),
            ],
        ];
    }

    public static function operation(): Operation
    {
        return Operation::In;
    }
}
