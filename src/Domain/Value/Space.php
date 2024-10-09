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

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final readonly class Space
{
    public Id $id;
    public string $name;
    public string $domain;
    public int $version;

    /**
     * @var string[]
     */
    public array $languageCodes;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values)
    {
        Assert::keyExists($values, 'id');
        $this->id = new Id($values['id']);

        Assert::keyExists($values, 'name');
        $this->name = TrimmedNonEmptyString::fromString($values['name'])->toString();

        Assert::keyExists($values, 'domain');
        $this->domain = TrimmedNonEmptyString::fromString($values['domain'])->toString();

        Assert::keyExists($values, 'version');
        Assert::integer($values['version']);
        Assert::greaterThan($values['version'], 0);
        $this->version = $values['version'];

        Assert::keyExists($values, 'language_codes');
        Assert::isArray($values['language_codes']);
        Assert::allString($values['language_codes']);
        $this->languageCodes = array_map(
            static fn (string $languageCode): string => TrimmedNonEmptyString::fromString($languageCode)->toString(),
            $values['language_codes'],
        );
    }
}
