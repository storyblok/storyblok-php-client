# storyblok-api

| Branch    | PHP                                         | Code Coverage                                        |
|-----------|---------------------------------------------|------------------------------------------------------|
| `master`  | [![PHP](https://github.com/sensiolabs-de/storyblok-api/actions/workflows/ci.yaml/badge.svg)](https://github.com/sensiolabs-de/storyblok-api/actions/workflows/ci.yaml)  | [![Code Coverage][coverage-status-master]][codecov]  |

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

[actions]: https://github.com/sensiolabs-de/storyblok-api/actions
[codecov]: https://codecov.io/gh/sensiolabs-de/storyblok-api
