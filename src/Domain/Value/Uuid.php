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
use Webmozart\Assert\Assert;
use function Symfony\Component\String\u;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
readonly class Uuid implements \Stringable
{
    public string $value;

    final public function __construct(string $value)
    {
        TrimmedNonEmptyString::fromString($value);
        // In rich text editor the UUID is prefixed with 'i-'
        $value = u($value)->trimStart('i-')->toString();
        Assert::uuid($value);
        Assert::true(strtolower($value) === $value, 'Uuid must be lowercase');

        $this->value = $value;
    }

    /**
     * Returns the value of the Uuid as a lowercase UUID string.
     */
    final public function __toString(): string
    {
        return $this->value;
    }
}
