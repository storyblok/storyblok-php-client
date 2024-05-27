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

namespace SensioLabs\Storyblok\Api;

use SensioLabs\Storyblok\Api\Domain\Value\Dto\Pagination;
use SensioLabs\Storyblok\Api\Response\DatasourceEntriesResponse;

/**
 * @author Simon André <smn.andre@gmail.com>
 */
interface DatasourceEntriesApiInterface
{
    public const int PER_PAGE = 25;
    public const int MAX_PER_PAGE = 1000;

    public function all(?Pagination $pagination = null): DatasourceEntriesResponse;

    public function allByDatasource(string $datasource, ?Pagination $pagination = null): DatasourceEntriesResponse;

    public function allByDimension(string $dimension, ?Pagination $pagination = null): DatasourceEntriesResponse;

    public function allByDatasourceDimension(string $datasource, string $dimension, ?Pagination $pagination = null): DatasourceEntriesResponse;
}
