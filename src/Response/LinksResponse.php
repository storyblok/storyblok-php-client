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

use SensioLabs\Storyblok\Api\Domain\Value\Link;
use SensioLabs\Storyblok\Api\Domain\Value\Total;
use Webmozart\Assert\Assert;

final readonly class LinksResponse
{
    /**
     * @var list<Link>
     */
    public array $links;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(
        public Total $total,
        array $values,
    ) {
        Assert::keyExists($values, 'links');
        $this->links = array_values(array_map(static fn (array $values): Link => new Link($values), $values['links']));
    }
}
