# storyblok-api

| Branch    | PHP                                         | Code Coverage                                                                                                                                       |
|-----------|---------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------|
| `master`  | [![PHP](https://github.com/sensiolabs-de/storyblok-api/actions/workflows/ci.yaml/badge.svg)](https://github.com/sensiolabs-de/storyblok-api/actions/workflows/ci.yaml)  | [![codecov](https://codecov.io/gh/sensiolabs-de/storyblok-api/graph/badge.svg?token=8K4F33LSWF)](https://codecov.io/gh/sensiolabs-de/storyblok-api) |

## Usage

### Installation

```bash
composer require sensiolabs-de/storyblok-api
```

### Setup

```php
use SensioLabs\Storyblok\Api\StoryblokClient;

$client = new StoryblokClient(
    baseUri: 'https://api.storyblok.com',
    token: '***********',
    timeout: 10 // optional
);

// you can now request any endpoint which needs authentication
$client->request('GET', '/api/something', $options);
```

## Spaces

In your code you should type-hint to `SensioLabs\Storyblok\Api\SpacesApiInterface`

### Get the current space

Returns the space associated with the current token.

```php
use SensioLabs\Storyblok\Api\SpacesApi;
use SensioLabs\Storyblok\Api\StoryblokClient;

$client = new StoryblokClient(/* ... */);
$spacesApi = new SpacesApi($client);

$response = $spacesApi->me();
```

## Stories

In your code you should type-hint to `SensioLabs\Storyblok\Api\StoriesApiInterface`

### Get all available stories

```php
use SensioLabs\Storyblok\Api\StoriesApi;
use SensioLabs\Storyblok\Api\StoryblokClient;

$client = new StoryblokClient(/* ... */);

$storiesApi = new StoriesApi($client);
$response = $storiesApi->all(locale: 'de');
```

### Pagination

```php
use SensioLabs\Storyblok\Api\StoriesApi;
use SensioLabs\Storyblok\Api\StoryblokClient;
use SensioLabs\Storyblok\Api\Domain\Value\Dto\Pagination;

$client = new StoryblokClient(/* ... */);

$storiesApi = new StoriesApi($client);
$response = $storiesApi->all(
    locale: 'de',
    pagination: new Pagination(page: 1, perPage: 30)
);
```

#### Sorting

```php
use SensioLabs\Storyblok\Api\StoriesApi;
use SensioLabs\Storyblok\Api\StoryblokClient;
use SensioLabs\Storyblok\Api\Domain\Value\Dto\SortBy;
use SensioLabs\Storyblok\Api\Domain\Value\Dto\Direction;

$client = new StoryblokClient(/* ... */);

$storiesApi = new StoriesApi($client);
$response = $storiesApi->all(
    locale: 'de',
    sortBy: new SortBy(field: 'title', direction: Direction::Desc)
);
```

#### Filtering

```php
use SensioLabs\Storyblok\Api\StoriesApi;
use SensioLabs\Storyblok\Api\StoryblokClient;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\FilterCollection;
use SensioLabs\Storyblok\Api\Domain\Value\Dto\Direction;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\InFilter;

$client = new StoryblokClient(/* ... */);

$storiesApi = new StoriesApi($client);
$response = $storiesApi->all(
    locale: 'de',
    filters: new FilterCollection([
        new InFilter(field: 'single_reference_field', value: 'f2fdb571-a265-4d8a-b7c5-7050d23c2383')
    ])
);
```

#### Available filters

[AllInArrayFilter.php](src/Domain/Value/Filter/Filters/AllInArrayFilter.php)

Example:
```php
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\AllInArrayFilter;

new AllInArrayFilter(field: 'tags', value: ['foo', 'bar', 'baz']);
```

[AnyInArrayFilter.php](src/Domain/Value/Filter/Filters/AnyInArrayFilter.php)

Example:
```php
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\AnyInArrayFilter;

new AnyInArrayFilter(field: 'tags', value: ['foo', 'bar', 'baz']);
```

[GreaterThanDateFilter.php](src/Domain/Value/Filter/Filters/GreaterThanDateFilter.php)

Example:
```php
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\GreaterThanDateFilter;

new GreaterThanDateFilter(field: 'created_at', value: new \DateTimeImmutable());
```

[LessThanDateFilter.php](src/Domain/Value/Filter/Filters/LessThanDateFilter.php)

Example:
```php
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\LessThanDateFilter;

new LessThanDateFilter(field: 'created_at', value: new \DateTimeImmutable());
```

[GreaterThanFloatFilter.php](src/Domain/Value/Filter/Filters/GreaterThanFloatFilter.php)

Example:
```php
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\GreaterThanFloatFilter;

new GreaterThanFloatFilter(field: 'price', value: 39.99);
```

[LessThanFloatFilter.php](src/Domain/Value/Filter/Filters/LessThanFloatFilter.php)

Example:
```php
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\LessThanFloatFilter;

new LessThanFloatFilter(field: 'price', value: 199.99);
```

[GreaterThanIntFilter.php](src/Domain/Value/Filter/Filters/GreaterThanIntFilter.php)

Example:
```php
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\GreaterThanIntFilter;

new GreaterThanIntFilter(field: 'stock', value: 0);
```

[LessThanIntFilter.php](src/Domain/Value/Filter/Filters/LessThanIntFilter.php)

Example:
```php
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\LessThanIntFilter;

new LessThanIntFilter(field: 'stock', value: 100);
```

[InFilter.php](src/Domain/Value/Filter/Filters/InFilter.php)

Example:
```php
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\InFilter;

new InFilter(field: 'text', value: 'Hello World!');
// or
new InFilter(field: 'text', value: ['Hello Symfony!', 'Hello SensioLabs!']);
```

[NotInFilter.php](src/Domain/Value/Filter/Filters/NotInFilter.php)

Example:
```php
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\NotInFilter;

new NotInFilter(field: 'text', value: 'Hello World!');
// or
new NotInFilter(field: 'text', value: ['Bye Symfony!', 'Bye SensioLabs!']);
```

[IsFilter.php](src/Domain/Value/Filter/Filters/IsFilter.php)

Example:
```php
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\IsFilter;

// You can use one of the following constants:
// IsFilter::EMPTY_ARRAY
// IsFilter::NOT_EMPTY_ARRAY
// IsFilter::EMPTY
// IsFilter::NOT_EMPTY
// IsFilter::TRUE
// IsFilter::FALSE
// IsFilter::NULL
// IsFilter::NOT_NULL

new IsFilter(field: 'text', value: IsFilter::EMPTY);
```

[LikeFilter.php](src/Domain/Value/Filter/Filters/LikeFilter.php)

Example:
```php
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\LikeFilter;

new LikeFilter(field: 'description', value: '*I love Symfony*');
```

[NotLikeFilter.php](src/Domain/Value/Filter/Filters/NotLikeFilter.php)

Example:
```php
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\NotLikeFilter;

new NotLikeFilter(field: 'description', value: '*Text*');
```

[OrFilter.php](src/Domain/Value/Filter/Filters/OrFilter.php)

Example:
```php
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\OrFilter;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\LikeFilter;
use SensioLabs\Storyblok\Api\Domain\Value\Filter\Filters\NotLikeFilter;

new OrFilter(
    new LikeFilter(field: 'text', value: 'Yes!*'),
    new LikeFilter(field: 'text', value: 'Maybe!*'),
    // ...
);
```

### Get all available stories by Content Type (`string`)

```php
use SensioLabs\Storyblok\Api\StoriesApi;
use SensioLabs\Storyblok\Api\StoryblokClient;

$client = new StoryblokClient(/* ... */);

$storiesApi = new StoriesApi($client);
$response = $storiesApi->allByContentType('custom_content_type', locale: 'de');
```

### Get by uuid (`SensioLabs\Storyblok\Api\Domain\Value\Uuid`)

```php
use SensioLabs\Storyblok\Api\StoriesApi;
use SensioLabs\Storyblok\Api\StoryblokClient;
use SensioLabs\Storyblok\Api\Domain\Value\Uuid;

$uuid = new Uuid(/** ... */);

$client = new StoryblokClient(/* ... */);

$storiesApi = new StoriesApi($client);
$response = $storiesApi->byUuid($uuid, locale: 'de');
```

### Get by slug (`string`)

```php
use SensioLabs\Storyblok\Api\StoriesApi;
use SensioLabs\Storyblok\Api\StoryblokClient;

$client = new StoryblokClient(/* ... */);

$storiesApi = new StoriesApi($client);
$response = $storiesApi->bySlug('folder/slug', locale: 'de');
```


### Get by id (`SensioLabs\Storyblok\Api\Domain\Value\Id`)

```php
use SensioLabs\Storyblok\Api\StoriesApi;
use SensioLabs\Storyblok\Api\StoryblokClient;
use SensioLabs\Storyblok\Api\Domain\Value\Id;

$id = new Id(/** ... */);

$client = new StoryblokClient(/* ... */);

$storiesApi = new StoriesApi($client);
$response = $storiesApi->byId($id, locale: 'de');
```


## Links

In your code you should type-hint to `SensioLabs\Storyblok\Api\LinksApiInterface`

### Get all available links

```php
use SensioLabs\Storyblok\Api\LinksApi;
use SensioLabs\Storyblok\Api\StoryblokClient;

$client = new StoryblokClient(/* ... */);

$linksApi = new LinksApi($client);
$response = $linksApi->all();
```

### Pagination

```php
use SensioLabs\Storyblok\Api\LinksApi;
use SensioLabs\Storyblok\Api\StoryblokClient;
use SensioLabs\Storyblok\Api\Domain\Value\Dto\Pagination;

$client = new StoryblokClient(/* ... */);

$linksApi = new LinksApi($client);
$response = $linksApi->all(
    pagination: new Pagination(page: 1, perPage: 1000)
);
```

### Get by parent (`SensioLabs\Storyblok\Api\Domain\Value\Id`)

```php
use SensioLabs\Storyblok\Api\LinksApi;
use SensioLabs\Storyblok\Api\StoryblokClient;
use SensioLabs\Storyblok\Api\Domain\Value\Id;

$id = new Id(/** ... */);

$client = new StoryblokClient(/* ... */);

$linksApi = new LinksApi($client);
$response = $linksApi->byParent($id);
```

### Get all root links

```php
use SensioLabs\Storyblok\Api\LinksApi;
use SensioLabs\Storyblok\Api\StoryblokClient;

$client = new StoryblokClient(/* ... */);

$linksApi = new LinksApi($client);
$response = $linksApi->roots($id);
```


## Datasource

In your code you should type-hint to `SensioLabs\Storyblok\Api\DatasourceApiInterface`

### Get by name (`string`)

```php
use SensioLabs\Storyblok\Api\DatasourceApi;
use SensioLabs\Storyblok\Api\StoryblokClient;

$client = new StoryblokClient(/* ... */);

$api = new DatasourceApi($client);
$response = $api->byName('tags'); // returns SensioLabs\Storyblok\Api\Domain\Value\Datasource
```

If it has more than one dimension, you can get the entries by

```php
use SensioLabs\Storyblok\Api\DatasourceApi;
use SensioLabs\Storyblok\Api\StoryblokClient;
use SensioLabs\Storyblok\Api\Domain\Value\Datasource\Dimension;

$client = new StoryblokClient(/* ... */);

$api = new DatasourceApi($client);
$response = $api->byName('tags', new Dimension('de')); // returns SensioLabs\Storyblok\Api\Domain\Value\Datasource
```

## Tags

In your code you should type-hint to `SensioLabs\Storyblok\Api\TagsApiInterface`

### Get all available tags

```php
use SensioLabs\Storyblok\Api\TagsApi;
use SensioLabs\Storyblok\Api\StoryblokClient;

$client = new StoryblokClient(/* ... */);

$api = new TagsApi($client);
$response = $api->all(); // returns SensioLabs\Storyblok\Api\Response\TagsResponse
```

## Symfony Support

### Flex recipe

If you install it in your Symfony flex project there is a recipe which will automatically configure the client for you.

* [Flex recipe](https://github.com/symfony/recipes-contrib/tree/main/sensiolabs-de/storyblok-api)

```yaml
# config/packages/sensiolabs_de_storyblok_api.yaml
services:
    _defaults:
        autowire: true

    SensioLabs\Storyblok\Api\StoryblokClient:
        arguments:
            - '%env(STORYBLOK_API_BASE_URI)%'
            - '%env(STORYBLOK_API_TOKEN)%'

    SensioLabs\Storyblok\Api\StoryblokClientInterface: '@SensioLabs\Storyblok\Api\StoryblokClient'

    SensioLabs\Storyblok\Api\DatasourceApi: null
    SensioLabs\Storyblok\Api\DatasourceApiInterface: '@SensioLabs\Storyblok\Api\DatasourceApi'

    SensioLabs\Storyblok\Api\DatasourceEntriesApi: null
    SensioLabs\Storyblok\Api\DatasourceEntriesApiInterface: '@SensioLabs\Storyblok\Api\DatasourceEntriesApi'

    SensioLabs\Storyblok\Api\StoriesApi: null
    SensioLabs\Storyblok\Api\StoriesApiInterface: '@SensioLabs\Storyblok\Api\StoriesApi'

    SensioLabs\Storyblok\Api\LinksApi: null
    SensioLabs\Storyblok\Api\LinksApiInterface: '@SensioLabs\Storyblok\Api\LinksApi'

    SensioLabs\Storyblok\Api\TagsApi: null
    SensioLabs\Storyblok\Api\TagsApiInterface: '@SensioLabs\Storyblok\Api\TagsApi'
```

### DX Enhancement through Abstract Collections

To improve developer experience (DX), especially when working with content types like stories, the following abstract
class is provided to manage collections of specific content types. This class simplifies data handling and ensures type
safety while dealing with large amounts of content from Storyblok.

#### Abstract ContentTypeCollection Class

The ContentTypeCollection class provides a structured way to work with Storyblok content types. It makes managing
pagination, filtering, and sorting more intuitive and reusable, saving time and reducing boilerplate code.

```php
<?php

declare(strict_types=1);

namespace App\ContentType;

use IteratorAggregate;
use SensioLabs\Storyblok\Api\Response\StoriesResponse;

/**
 * @template T of ContentTypeInterface
 *
 * @implements IteratorAggregate<int, T>
 */
abstract readonly class ContentTypeCollection implements \Countable, \IteratorAggregate
{
    public int $total;
    public int $perPage;
    public int $curPage;
    public int $lastPage;
    public ?int $prevPage;
    public ?int $nextPage;

    /**
     * @var list<T>
     */
    private array $items;

    final public function __construct(StoriesResponse $response)
    {
        $this->items = array_values(array_map($this->createItem(...), $response->stories));

        $this->total = $response->total->value;
        $this->curPage = $response->pagination->page;
        $this->perPage = $response->pagination->perPage;

        $this->lastPage = (int) ceil($this->total / $this->perPage);
        $this->prevPage = 1 < $this->curPage ? $this->curPage - 1 : null;
        $this->nextPage = $this->curPage < $this->lastPage ? $this->curPage + 1 : null;
    }

    /**
     * @return \Traversable<int, T>
     */
    final public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    final public function count(): int
    {
        return \count($this->items);
    }

    /**
     * @param array<string, mixed> $values
     *
     * @return T
     */
    abstract protected function createItem(array $values): ContentTypeInterface;
}
```

#### Benefits of Using the Abstract Collection:

1. **Simplified Data Handling:** Instead of dealing with raw arrays of stories, this abstract class helps you manage
   collections of content types, like blog posts or articles, in an organized manner. It abstracts away the repetitive
   work of pagination and mapping response data to objects.
2. **Enhanced Readability:** Using a well-structured collection class makes the code easier to read and maintain. Instead of
   handling pagination and raw data structures in controllers or services, you simply instantiate the collection and let
   it handle the data.
3. **Reusability:** The class is flexible and reusable across different content types. Once implemented, you can easily
   create new collections for other Storyblok content types with minimal extra code.
4. **Pagination and Metadata Management:** The collection class comes with built-in properties for pagination and
   metadata (e.g., total items, current page, etc.), making it much easier to manage paginated data efficiently.

### Example Usage with a Collection

Here is an example of how to use the ContentTypeCollection to manage blog posts in your Symfony project:

```php
<?php

declare(strict_types=1);

namespace App\ContentType\BlogPost;

use App\ContentType\ContentTypeCollection;
use App\ContentType\ContentTypeFactory;

/**
 * @extends ContentTypeCollection<BlogPost>
 */
final readonly class BlogPostCollection extends ContentTypeCollection
{
    protected function createItem(array $values): BlogPost
    {
        return ContentTypeFactory::create($values, BlogPost::class);
    }
}
```

```php
new BlogPostCollection(
    $this->stories->allByContentType(
        BlogPost::type(),
        new StoriesRequest(
            language: $this->localeSwitcher->getLocale(),
            pagination: new Pagination($this->curPage, self::PER_PAGE),
            sortBy: new SortBy('first_published_at', Direction::Desc),
            filters: $filters,
            excludeFields: new FieldCollection([
                new Field('body'),
                new Field('additional_contents'),
            ]),
        ),
    ),
);
```

[actions]: https://github.com/sensiolabs-de/storyblok-api/actions
[codecov]: https://codecov.io/gh/sensiolabs-de/storyblok-api
