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

namespace SensioLabs\Storyblok\Api\Response;

use SensioLabs\Storyblok\Api\Domain\Value\Tag;
use Webmozart\Assert\Assert;

final readonly class TagsResponse
{
    /**
     * @var list<Tag>
     */
    public array $tags;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(
        array $values,
    ) {
        Assert::keyExists($values, 'tags');
        Assert::isArray($values['tags']);
        Assert::allIsArray($values['tags']);
        Assert::allKeyExists($values['tags'], 'name');
        Assert::allKeyExists($values['tags'], 'taggings_count');

        $this->tags = array_map(static fn (array $tag) => new Tag($tag['name'], $tag['taggings_count']), $values['tags']);
    }
}
