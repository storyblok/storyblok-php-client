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

final readonly class Link
{
    public Id $id;
    public Uuid $uuid;
    public ?Id $parentId;
    public string $name;
    public string $slug;
    public ?string $path;
    public string $realPath;
    public int $position;
    public bool $isFolder;
    public bool $isStartPage;
    public bool $isPublished;

    /**
     * @var list<LinkAlternate>1
     */
    public array $alternates;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values)
    {
        Assert::keyExists($values, 'id');
        Assert::integer($values['id']);
        $this->id = new Id($values['id']);

        Assert::keyExists($values, 'parent_id');

        if (null !== $values['parent_id']) {
            Assert::integer($values['parent_id']);

            if (0 !== $values['parent_id']) {
                $parentId = new Id($values['parent_id']);
            }
        }

        $this->parentId = $parentId ?? null;

        Assert::keyExists($values, 'uuid');
        $this->uuid = new Uuid($values['uuid']);

        Assert::keyExists($values, 'name');
        $this->name = TrimmedNonEmptyString::fromString($values['name'])->toString();

        Assert::keyExists($values, 'slug');
        $this->slug = TrimmedNonEmptyString::fromString($values['slug'])->toString();

        Assert::keyExists($values, 'path');

        if (null !== $values['path']) {
            $path = TrimmedNonEmptyString::fromString($values['path'])->toString();
        }

        $this->path = $path ?? null;

        Assert::keyExists($values, 'real_path');
        $this->realPath = TrimmedNonEmptyString::fromString($values['real_path'])->toString();

        Assert::keyExists($values, 'position');
        Assert::integer($values['position']);
        $this->position = $values['position'];

        Assert::keyExists($values, 'is_folder');
        $this->isFolder = true === $values['is_folder'];

        Assert::keyExists($values, 'is_startpage');
        $this->isStartPage = true === $values['is_startpage'];

        Assert::keyExists($values, 'published');
        $this->isPublished = true === $values['published'];

        Assert::keyExists($values, 'alternates');
        Assert::isArray($values['alternates']);
        $this->alternates = array_map(static fn (array $values) => new LinkAlternate($values), $values['alternates']);
    }

    public function isFolder(): bool
    {
        return $this->isFolder;
    }

    public function isPublished(?string $lang = null): bool
    {
        if (null === $lang) {
            return $this->isPublished;
        }

        return $this->getAlternate($lang)->published;
    }

    public function isStartPage(): bool
    {
        return $this->isStartPage;
    }

    public function isStory(): bool
    {
        return !$this->isFolder;
    }

    public function getSlug(?string $lang = null): string
    {
        if (null === $lang) {
            return $this->slug;
        }

        return $this->getAlternate($lang)->slug;
    }

    public function getName(?string $lang = null): string
    {
        if (null === $lang) {
            return $this->name;
        }

        return $this->getAlternate($lang)->name ?? $this->name;
    }

    private function getAlternate(string $lang): LinkAlternate
    {
        foreach ($this->alternates as $alternate) {
            if ($alternate->lang === $lang) {
                return $alternate;
            }
        }

        throw new \InvalidArgumentException(\sprintf('Alternate for language "%s" not found', $lang));
    }
}
