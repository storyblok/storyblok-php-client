<div style="text-align:center">
	<a  href="https://www.storyblok.com?utm_source=github.com&utm_medium=readme&utm_campaign=php-client"  align="center">
		<img  src="https://a.storyblok.com/f/88751/1776x360/dc6e51a5fd/sb-php.png"  alt="Storyblok Logo">
	</a>
	<h1  style="text-align:center">Storyblok PHP Client</h1>
	<p  style="text-align:center">This is the official <a href="https://www.storyblok.com?utm_source=github.com&utm_medium=referral&utm_campaign=php-client">Storyblok</a> PHP client to easy access the content deliver api and management api.</p>
</div>

<p style="text-align:center">
  <a href="https://discord.gg/jKrbAMz">
   <img src="https://img.shields.io/discord/700316478792138842?label=Join%20Our%20Discord%20Community&style=appveyor&logo=discord&color=09b3af" alt="Join the Storyblok Discord Community">
   </a>
  <a href="https://twitter.com/intent/follow?screen_name=storyblok">
    <img src="https://img.shields.io/badge/Follow-%40storyblok-09b3af?style=appveyor&logo=twitter" alt="Follow @Storyblok" />
  </a><br/>
  <a href="https://app.storyblok.com/#!/signup?utm_source=github.com&utm_medium=readme&utm_campaign=php-client">
    <img src="https://img.shields.io/badge/Try%20Storyblok-Free-09b3af?style=appveyor&logo=data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAABGdBTUEAALGPC/xhBQAAADhlWElmTU0AKgAAAAgAAYdpAAQAAAABAAAAGgAAAAAAAqACAAQAAAABAAAAHqADAAQAAAABAAAAHgAAAADpiRU/AAACRElEQVRIDWNgGGmAEd3D3Js3LPrP8D8WXZwSPiMjw6qvPoHhyGYwIXNAbGpbCjbzP0MYuj0YFqMroBV/wCxmIeSju64eDNzMBJUxvP/9i2Hnq5cM1devMnz984eQsQwETeRhYWHgIcJiXqC6VHlFBjUeXgav40cIWkz1oLYXFmGwFBImaDFBHyObcOzdW4aSq5eRhRiE2dgYlpuYoYSKJi8vw3GgWnyAJIs/AuPu4scPGObd/fqVQZ+PHy7+6udPOBsXgySLDfn5GRYYmaKYJcXBgWLpsx8/GPa8foWiBhuHJIsl2DkYQqWksZkDFgP5PObcKYYff//iVAOTIDlx/QPqRMb/YSYBaWlOToZIaVkGZmAZSQiQ5OPtwHwacuo4iplMQEu6tXUZMhSUGDiYmBjylFQYvv/7x9B04xqKOnQOyT5GN+Df//8M59ASXKyMHLoyDD5JPtbj42OYrm+EYgg70JfuYuIoYmLs7AwMjIzA+uY/zjAnyWJpDk6GOFnCvrn86SOwmsNtKciVFAc1ileBHFDC67lzG10Yg0+SjzF0ownsf/OaofvOLYaDQJoQIGix94ljv1gIZI8Pv38zPvj2lQWYf3HGKbpDCFp85v07NnRN1OBTPY6JdRSGxcCw2k6sZuLVMZ5AV4s1TozPnGGFKbz+/PE7IJsHmC//MDMyhXBw8e6FyRFLv3Z0/IKuFqvFyIqAzd1PwBzJw8jAGPfVx38JshwlbIygxmYY43/GQmpais0ODDHuzevLMARHBcgIAQAbOJHZW0/EyQAAAABJRU5ErkJggg==" alt="Follow @Storyblok" />
  </a>
</p>

## 🚀 Usage
With the Storyblok PHP client you can integrate two kinds of Storyblok APIs:

- Management API: typically used for managing data, like create data, blocks, settings etc.
- Content Delivery API: typically used for retrieving data, for example when you want to build your public Web application.

Topics covered:
- [Installing Storyblok PHP client](#install)
- [Using the Management API with the Storyblok PHP client](#management-api)
- [Using the Content Delivery API with the Storyblok PHP client](#content-delivery-api)


## Install
You can install the Storyblok PHP Client via composer.
You need to have composer installed on your development environment.
If you want to install the _stable_ release:
```bash
composer require storyblok/php-client
```

If you want to install the _current_ development release:
```bash
composer require storyblok/php-client dev-master
```

If you need to install Composer, you can follow the official Composer documentation:

- Install Composer on [GNU Linux / Unix / macOS](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)
- Install Composer on [Windows](https://getcomposer.org/doc/00-intro.md#installation-windows)

We suggest to use the latest version of PHP.

## Management API

### The Storyblok\ManagementClient instance

Initialize the Storyblok Management Client  for the [Management API](https://www.storyblok.com/docs/api/management) with your Personal OAuth Token.
The Personal OAuth token is taken from the my account section for read and write operations.

```php
<?php
// Require composer autoload
require 'vendor/autoload.php';
// Use the Storyblok\ManagementClient class
use Storyblok\ManagementClient;
// Use the ManagementClient class
$managementClient = new ManagementClient('your-storyblok-oauth-token');
```

Now, you have the instance of `ManagementClient` you can start to manage your Storyblok data

### Retrieve data, get() method
If you need to retrieve data, you have to perform a HTTP request with GET method.
The ManagementClient provides a `get()` method for performing the HTTP request.
The mandatory parameters is the path of the API ( for example `spaces/<yourSpaceId/stories`).
For retrieving a list of Stories:
```php
$spaceId = 'YOUR_SPACE_ID';
$result = $managementClient->get('spaces/' . $spaceId . '/stories')->getBody();
print_r($result['stories']);
```

### Create data, post() method
If you need to create data, you have to perform a HTTP request with POST method.
The ManagementClient provides a `post()` method for performing the HTTP request.
The mandatory parameters is the path of the API ( for example `spaces/<yourSpaceId/stories/`), and the Story payload.
For creating a new story:

```php
$spaceId = 'YOUR_SPACE_ID';
$story = [
    "name" => "New Page",
    "slug" => "page-1",
    "content" =>  [
        "component" =>  "page",
        "body" =>  []
    ]
];
$result = $managementClient->post(
    'spaces/' . $spaceId . '/stories/',
    [ 'space' => $story ]
    )->getBody();
print_r($result);
```
### Update data, put() method
If you need to update data, you have to perform an HTTP request with PUT method.
The ManagementClient provides a `put()` method for performing the HTTP request.
The mandatory parameters is the path of the API ( for example `spaces/<yourSpaceId/stories/<storyId>`), and the Story payload.
For updating story:

```php
$spaceId = 'YOUR_SPACE_ID';
$storyId= 'theStoryId';
$story = [
    "name" => "Update Home Page"
];
$result = $managementClient->put(
    'spaces/' . $spaceId . '/stories/' . $storyId,
    [ 'space' => $story ]
    )->getBody();
print_r($result);
```

### Delete data, delete() method
If you need to delete data, you have to perform an HTTP request with DELETE method.
The ManagementClient provides a `delete()` method for performing the HTTP request.
The mandatory parameters is the path of the API ( for example `spaces/<yourSpaceId/stories/<storyId>`).
For deleting a story:


```php
$spaceId = 'YOUR_SPACE_ID';
$storyId = 'YOUR_STORY_ID';
$result = $managementClient->delete('spaces/' . $spaceId . '/stories/' . $storyId)->getbody();
print_r($result);
```


## Content Delivery API

### The Storyblok\Client instance

Initialize the Storyblok Client class for consume the [Content Delivery API V2](https://www.storyblok.com/docs/api/content-delivery/v2).
You have to use the Access token to access to the content.

```php
<?php
// Require composer autoload
require 'vendor/autoload.php';
// Use the Storyblok\Client class
use Storyblok\Client;
// Use the Client class
$client = new Client('your-storyblok-draft-token');
```

### Using spaces created in US region

When you create a Space, you can select the region: EU or US.
If you want to access to a Space created in US region, you need to define the `apiREgion` parameter with 'us' (or 'US'):

```php
use Storyblok\Client;

$client = new Client(
    apiKey: 'your-storyblok-draft-token',
    apiRegion: 'us'
);
```

If you are still using PHP 7.x, you have to use the old notation (without named arguments):
```php
use Storyblok\Client;
$client = new Client(
    'your-storyblok-draft-token',
    null,
    'v2',
    false,
    'us'
);
```

Now you have the `Storyblok\Client` instance you can start consuming data

### Load a Story by slug

```php
require 'vendor/autoload.php';
use Storyblok\Client as StoryblokClient; // you can use also an alias
$client = new StoryblokClient('your-storyblok-private-token');
$data = $client->getStoryBySlug('home')->getBody();
print_r($data["story"]);
echo $data["cv"] . PHP_EOL;
print_r($data["rels"]);
print_r($data["links"]);
```
Once you obtain the body of the response you can use the `getBody()` method for retrieving a structured associative array. With the body of the response, you can access to:
- `story`: the story
- `cv`: the cache timestamp (useful for managing cached response)
- `rels`: the (optional) relations
- `links`: the resolved links

### Load a Story by UUID

```php
require 'vendor/autoload.php';
use Storyblok\Client as StoryblokClient; // you can use also an alias
$client = new StoryblokClient('your-storyblok-private-token');
$client->getStoryByUuid('0c092d14-5cd4-477e-922c-c7f8e330aaea');
$data = $client->getBody();
```
The structure of the data returned by the `getBody()` of the `getStoryByUuid()` method, has the same structure of the `getStoryBySlug()` so: `story`, `cv`, `rels`, `links`



### Load a list of Stories

```php
// Require composer autoload
require 'vendor/autoload.php';

// Initialize
$client = new \Storyblok\Client('your-storyblok-private-token');

// Optionally set a cache
$client->setCache('filesytem', array('path' => 'cache'));

// Get all Stories that start with news
$client->getStories(
    array(
        'starts_with' => 'news'
    )
);
$data = $client->getStoryContent();
```

### Load a list of datasource entries

```php
// Require composer autoload
require 'vendor/autoload.php';

// Initialize
$client = new \Storyblok\Client('your-storyblok-private-token');

// Optionally set a cache
$client->setCache('filesytem', array('path' => 'cache'));

// Get category entries from datasource
$client->getDatasourceEntries('categories');

// will return the whole response
$data = $client->getBody();

// will return as ['name']['value'] Array for easy access
$nameValueArray = $client->getAsNameValueArray();

```

If you want to receive also the dimension values besides the default values in one datasource entry you can use the option _dimension_ when you call _getDatasourceEntries()_ method.
You could use dimensions for example when you are using datasource for storing a list of values and you want a translation for the values. In this case, you should create one dimension for each language.

```php
require 'vendor/autoload.php';
$client = new \Storyblok\Client('your-storyblok-private-token');
// Get product entries with dimension 'de-at'
$client->getDatasourceEntries('products', ['dimension'=> 'de-at']);
// show the dimension values:
foreach ($client->getBody()['datasource_entries'] as $key => $value) {
    echo $value['dimension_value'] . PHP_EOL;
}

```


### Load a list of tags

```php
// Require composer autoload
require 'vendor/autoload.php';

// Initialize
$client = new \Storyblok\Client('your-storyblok-private-token');

// Optionally set a cache
$client->setCache('filesytem', array('path' => 'cache'));

// Get all Tags
$client->getTags();

// will return the whole response
$data = $client->getBody();

// will return as ['tagName1', 'tagName2'] Array for easy access
$stringArray = $client->getAsStringArray();

```

### Load a list of tags and get the Respones Headers

```php
// Require composer autoload
require 'vendor/autoload.php';

// Initialize
$client = new \Storyblok\Client('your-storyblok-private-token');

// Optionally set a cache
$client->setCache('filesytem', array('path' => 'cache'));

// Get all Tags
$client->getTags();

// Let's you acces the Headers
var_dump($client->getHeaders());

```

## Managing cache
The content delivery client checks the get parameters _storyblok to get the draft version of a specific story and _storyblok_published to clear the cache.

### Clearing the cache (Optionally if using setCache)

In order to flush the cache when the user clicks publish, you need to listen to the published event in javascript or define a webhook in the space settings that clears the cache on your server.

```html
<script type="text/javascript" src="//app.storyblok.com/f/storyblok-latest.js"></script>
<script type="text/javascript">
    storyblok.init()

    storyblok.on('published', function() {
        $.ajax({
            url: '/clear.php'
        })
    })
</script>
```

In clear.php:
```php
$client = new \Storyblok\Client('your-storyblok-private-token');
$client->setCache('filesystem', array('path' => 'cache'));

// Flush the whole cache when a story has been published
$client->flushCache();

// Or empty the cache for one specific item only
$client->deleteCacheBySlug('home');
```


### Generate a navigation tree

```php
$tree = $client->editMode()->getLinks()->getAsTree();

echo '<ul>';
foreach ($tree as $item) {
    echo '<li>' . $item['item']['name'];

    if (!empty($item['children'])) {
        echo '<ul>';
        foreach ($item['children'] as $item2) {
            echo '<li>' . $item2['item']['name'] . '</li>';
        }
        echo '</ul>';
    }

    echo '</li>';
}
echo '</ul>';
```

#### Nginx SSI - Server Side Includes

Use the following script if you have Nginx SSI enabled and experience issues with printing the _editable html comments directly to manually parse the Storyblok HTML editable comments: https://gist.github.com/DominikAngerer/ca61d41bae3afcc646cfee286579ad36

### Relationships and Links Resolving

In order to resolve relations you can use the `resolveRelations` method of the client passing a comma separated list of fields:

```php
$client = new \Storyblok\Client('your-storyblok-private-token');

$client->resolveRelations('component_name1.field_name1,component_name2.field_name2')
$client->getStoryBySlug('home');
```

In order to resolve links, you can use the `resolveLinks` method passing the specific type of resolving you want to perform among `url`, `story` or `link`:

```php
$client = new \Storyblok\Client('your-storyblok-private-token');

$client->resolveLinks('url')
$client->getStoryBySlug('home');
```

When using the CDN API V1, you can't resolve relationships of resolved entries and the resolved entries are injected in the field of the relationship. The same happens with links resolving. 
When using the CDN API V2 you can resolve also nested relationships in the resolved entries (just 2 levels deep), but the resolved entries are not injected in the fields, they are inserted in an array called `rels` which is in the root object. The resolved links will be placed in an array called `links`.
In case you are using the API V2, to keep a consistent behaviour with the API V1, this client will inject the resolved entries and links inside the fields for you.

## Code Quality

The package includes tools for tests and code formatting:
- [PestPHP](https://pestphp.com/)
- [PHP CS Fixer](https://cs.symfony.com/)
To execute the code quality suite you can use:
```shell
composer run all-check
```
that executes:
- vendor/bin/php-cs-fixer fix
- vendor/bin/pest



## 🔗 Related Links

* **[Storyblok & PHP on GitHub](https://github.com/search?q=org%3Astoryblok+topic%3Aphp)**:  Check all of our PHP open source repos;
* **[Storyblok PHP Richtext Renderer](https://github.com/storyblok/storyblok-php-richtext-renderer)**: This package allows you to get an HTML string from the richtext field of Storyblok;
* **[Storyblok Laravel Tutorial](https://www.storyblok.com/tp/add-a-headless-cms-to-laravel-in-5-minutes?utm_source=github.com&utm_medium=referral&utm_campaign=php-client)** : Add a Headless CMS to Laravel in 5 minutes.

## ℹ️ More Resources

### Support

* Bugs or Feature Requests? [Submit an issue](../../../issues/new);

* Do you have questions about Storyblok or you need help? [Join our Discord Community](https://discord.gg/jKrbAMz).

### Contributing

Please see our [contributing guidelines](https://github.com/storyblok/.github/blob/master/contributing.md) and our [code of conduct](https://www.storyblok.com/trust-center#code-of-conduct?utm_source=github.com&utm_medium=readme&utm_campaign=php-client).
This project use [semantic-release](https://semantic-release.gitbook.io/semantic-release/) for generate new versions by using commit messages and we use the Angular Convention to naming the commits. Check [this question](https://semantic-release.gitbook.io/semantic-release/support/faq#how-can-i-change-the-type-of-commits-that-trigger-a-release) about it in semantic-release FAQ.

### License

This repository is published under the [MIT](./LICENSE) license.
