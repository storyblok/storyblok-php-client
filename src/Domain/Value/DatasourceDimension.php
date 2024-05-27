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
use Safe\DateTimeImmutable;
use Webmozart\Assert\Assert;

/**
 * @see https://www.storyblok.com/docs/api/content-delivery/v2/datasources/the-datasource-object
 */
final readonly class DatasourceDimension
{
    public Id $id;
    public string $name;
    public string $entryValue;
    public Id $datasourceId;
    public \DateTimeImmutable $createdAt;
    public \DateTimeImmutable $updatedAt;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values)
    {
        Assert::keyExists($values, 'id');
        $this->id = new Id($values['id']);

        Assert::keyExists($values, 'name');
        $this->name = TrimmedNonEmptyString::fromString($values['name'])->toString();

        Assert::keyExists($values, 'entry_value');
        $this->entryValue = TrimmedNonEmptyString::fromString($values['entry_value'])->toString();

        Assert::keyExists($values, 'datasource_id');
        $this->datasourceId = new Id($values['datasource_id']);

        Assert::keyExists($values, 'created_at');
        $this->createdAt = new DateTimeImmutable($values['created_at']);

        Assert::keyExists($values, 'updated_at');
        $this->updatedAt = new DateTimeImmutable($values['updated_at']);
    }
}
