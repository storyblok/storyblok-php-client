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
 *
 * @see https://www.storyblok.com/docs/api/content-delivery/v2/datasources/the-datasource-object
 */
final readonly class Datasource
{
    public Id $id;

    /**
     * The complete name provided for the datasource.
     */
    public string $name;

    /**
     * The unique slug of the datasource.
     */
    public string $slug;

    /**
     * The dimensions (e.g., per country, region, language, or other context) defined for the datasource.
     *
     * @var list<DatasourceDimension>
     */
    public array $dimensions;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values)
    {
        Assert::keyExists($values, 'id');
        $this->id = new Id($values['id']);

        Assert::keyExists($values, 'name');
        $this->name = TrimmedNonEmptyString::fromString($values['name'])->toString();

        Assert::keyExists($values, 'slug');
        $this->slug = TrimmedNonEmptyString::fromString($values['slug'])->toString();

        Assert::keyExists($values, 'dimensions');
        $this->dimensions = array_map(
            static fn (array $dimension) => new DatasourceDimension($dimension),
            $values['dimensions'],
        );
    }
}
