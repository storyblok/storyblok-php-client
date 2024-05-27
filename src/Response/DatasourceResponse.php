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

use SensioLabs\Storyblok\Api\Domain\Value\Datasource;
use Webmozart\Assert\Assert;

final readonly class DatasourceResponse
{
    public Datasource $datasource;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(
        array $values,
    ) {
        Assert::keyExists($values, 'datasource');
        $this->datasource = new Datasource($values['datasource']);
    }
}
