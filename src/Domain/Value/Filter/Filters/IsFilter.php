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

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final readonly class IsFilter extends Filter
{
    public const string EMPTY_ARRAY = 'empty_array';
    public const string NOT_EMPTY_ARRAY = 'not_empty_array';
    public const string EMPTY = 'empty';
    public const string NOT_EMPTY = 'not_empty';
    public const string TRUE = 'true';
    public const string FALSE = 'false';
    public const string NULL = 'null';
    public const string NOT_NULL = 'not_null';

    /**
     * @param self::* $value
     */
    public function __construct(
        private string $field,
        private string $value,
    ) {
        TrimmedNonEmptyString::fromString($field);
        Assert::oneOf($value, [
            self::EMPTY_ARRAY,
            self::NOT_EMPTY_ARRAY,
            self::EMPTY,
            self::NOT_EMPTY,
            self::TRUE,
            self::FALSE,
            self::NULL,
            self::NOT_NULL,
        ]);
    }

    public function toArray(): array
    {
        return [
            $this->field => [
                self::operation()->value => $this->value,
            ],
        ];
    }

    public function field(): string
    {
        return $this->field;
    }

    public static function operation(): Operation
    {
        return Operation::Is;
    }
}
