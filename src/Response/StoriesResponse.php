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

use SensioLabs\Storyblok\Api\Domain\Value\Total;
use Webmozart\Assert\Assert;

final readonly class StoriesResponse
{
    /**
     * @var list<array<string, mixed>>
     */
    public array $stories;
    public int $cv;

    /**
     * @var list<string>
     */
    public array $rels;

    /**
     * @var list<string>
     */
    public array $links;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(
        public Total $total,
        array $values,
    ) {
        Assert::keyExists($values, 'stories');
        $this->stories = $values['stories'];

        Assert::keyExists($values, 'cv');
        Assert::integer($values['cv']);
        $this->cv = $values['cv'];

        Assert::keyExists($values, 'rels');
        $this->rels = $values['rels'];

        Assert::keyExists($values, 'links');
        $this->links = $values['links'];
    }
}
