<div style="text-align:center">
	<a  href="https://www.storyblok.com?utm_source=github.com&utm_medium=readme&utm_campaign=php-client"  align="center">
		<img  src="https://a.storyblok.com/f/88751/1776x360/dc6e51a5fd/sb-php.png"  alt="Storyblok Logo">
	</a>
	<h1  style="text-align:center">Storyblok PHP Client</h1>
	<p  style="text-align:center">This is the official <a href="https://www.storyblok.com?utm_source=github.com&utm_medium=referral&utm_campaign=php-client">Storyblok</a> PHP client to easily access the Content Delivery API and Management API.</p>
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
With the **Storyblok PHP client** you can integrate two kinds of Storyblok APIs:

- Management API: typically used for managing data, like creating data, blocks, settings etc.
- Content Delivery API: typically used for retrieving data, for example when you want to build your public Web application.

In this README file you will find information for using the Storyblok PHP client, like:

- [Installing Storyblok PHP client](#installing-the-storyblok-php-client)
- [Using the Management API](#management-api)
- [Using the Content Delivery API](#content-delivery-api)
- [Retrieving Draft or Published content](#retrieving-draft-or-published-content)
- [Managing cache](#managing-cache)
- [Resolve Relations and Links](#relationships-and-links-resolving)


## Installing the Storyblok PHP client
You can install the Storyblok PHP Client via composer.
Storyblok's PHP client requires PHP version 7.3 to 8.2.
The suggestion is to use an actively supported version of PHP (8.1 and 8.2).

If you want to install the _stable_ release of Storyblok PHP client you can launch:
```bash
composer require storyblok/php-client
```

If you want to install the _current_ development release, you can add the version `dev-master`:
```bash
composer require storyblok/php-client dev-master
```

For executing the command above, you need to have composer installed on your development environment.
If you need to install Composer, you can follow the official Composer documentation:

- Install Composer on [GNU Linux / Unix / macOS](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)
- Install Composer on [Windows](https://getcomposer.org/doc/00-intro.md#installation-windows)

We suggest using the latest version of PHP.

## Management API

### The Storyblok\ManagementClient instance

Now we are going to see how to initialize the Storyblok Management Client for the [Management](https://www.storyblok.com/docs/api/management) API](https://www.storyblok.com/docs/api/management) with your Personal OAuth Token.
The Personal OAuth token is taken from the "My Account" section. This token is used for read and write operations.
The class for using the Management API is the `Storyblok\ManagementClient` class. When you are going to instance a new `ManagementClient` object you can use the Personal OAuth Token as a parameter.

```php
<?php
// Require composer autoload
require 'vendor/autoload.php';
// Use the Storyblok\ManagementClient class
use Storyblok\ManagementClient;
// Use the ManagementClient class
$managementClient = new ManagementClient('your-storyblok-oauth-token');
```

Now, you have the `ManagementClient` object (`$managementClient`), you can start to manage your Storyblok data.

### Retrieve data, get() method
If you need to retrieve data, you have to perform an HTTP request with the `GET` method.
The ManagementClient provides a `get()` method for performing the HTTP request.
The mandatory parameter is the path of the API ( for example `spaces/<yourSpaceId/stories`). The path defines which endpoint you want to use.

For retrieving a list of Stories:
```php
$spaceId = 'YOUR_SPACE_ID';
$result = $managementClient->get('spaces/' . $spaceId . '/stories')->getBody();
print_r($result['stories']);
```

With `getBody()` method you can access the body response, and then access the `'`stories'` key, to access the story list.

### Create data, post() method
If you need to create data, you have to perform an HTTP request with the `POST` method.
The ManagementClient provides a `post()` method for performing the HTTP request.
The mandatory parameter is the path of the API ( for example `spaces/<yourSpaceId/stories/`), and the Story payload.
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
If you need to update data, you have to perform an HTTP request with `PUT` method.
The ManagementClient provides a `put()` method for performing the HTTP request.
The mandatory parameter is the path of the API ( for example `spaces/<yourSpaceId/stories/<storyId>`), and the Story payload.
For updating the story:

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

If you need to delete data, you have to perform an HTTP request with the `DELETE` method.
The ManagementClient provides a `delete()` method for performing the HTTP request.
The mandatory parameter is the path of the API, defining also the identifier of the entry you want to delete ( for example `spaces/<yourSpaceId/stories/<storyId>`).
For deleting a story:


```php
$spaceId = 'YOUR_SPACE_ID';
$storyId = 'YOUR_STORY_ID';
$result = $managementClient->delete('spaces/' . $spaceId . '/stories/' . $storyId)->getbody();
print_r($result);
```

### Using spaces created in other regions for Management API

When creating a Space, you can select the EU, US, CA, AP region. The default region is EU.  
```
EU: api.storyblok.com
US: api-us.storyblok.com
CA: api-ca.storyblok.com
AP: api-ap.storyblok.com
```

For example:
If you want to access a Space created in US region, you need to define the `apiEndpoint` parameter with `api-us.storyblok.com` value, and forcing the `ssl` option for using HTTPS protocol:

```php
use Storyblok\ManagementClient;

$client = new ManagementClient(
    apiKey: 'your-storyblok-oauth-token',
    apiEndpoint: "api-us.storyblok.com",
    ssl : true
);
```


Now you have the `Storyblok\ManagementClient` instance, you can start managing data.

## Content Delivery API

### The Storyblok\Client instance

Now we are going to see how to initialize the Storyblok Client class for consuming the [Content Delivery API V2](https://www.storyblok.com/docs/api/content-delivery/v2), with the Access Token.
You can retrieve the access token from the "Settings > Access Tokens" tab (in your space, in Stroyblok UI).


```php
<?php
// Require composer autoload
require 'vendor/autoload.php';
// Use the Storyblok\Client class
use Storyblok\Client;
// Use the Client class
$client = new Client('your-storyblok-readonly-accesstoken');
```

If you want to use an alias to refer to the `Storyblok\Client` class, you can use the `use ... as ...` statement:

```php
require 'vendor/autoload.php';
use Storyblok\Client as StoryblokClient;
// Use the Storyblok\Client class via alias
$client = new StoryblokClient('your-storyblok-readonly-accesstoken');
```

### Using spaces created in the other regions

When you create a Space, you can select the region: EU, US, CA or AP.  

For example:
If you want to access a Space created in US region, you need to define the `apiRegion` parameter with the `us` value (or `US`):

```php
use Storyblok\Client;

$client = new Client(
    apiKey: 'your-storyblok-readonly-accesstoken',
    apiRegion: 'us'
);
```

If you are still using PHP 7.x, you have to use the old notation (without named arguments):
```php
use Storyblok\Client;
$client = new Client(
    'your-storyblok-readonly-accesstoken',
    null,
    'v2',
    false,
    'us'
);
```

Now you have the `Storyblok\Client` instance you can start consuming data. 

### Load a Story by slug

```php
require 'vendor/autoload.php';
use Storyblok\Client as StoryblokClient; // you can use also an alias
$client = new StoryblokClient('your-storyblok-readonly-accesstoken');
$data = $client->getStoryBySlug('home')->getBody();
// access to the body response...
print_r($data["story"]);
echo $data["cv"] . PHP_EOL;
print_r($data["rels"]);
print_r($data["links"]);
```

### Load a Story by slug for a specific language

If are using the [field-level translation](https://www.storyblok.com/tp/setup-field-and-folder-level-translation), you can retrieve a story for a specific language via the `language()` method. The language method requires a string as a parameter with the code of the language.

```php
require 'vendor/autoload.php';
use Storyblok\Client;
$client = new Client('your-storyblok-readonly-accesstoken');
$client->language('it');
$data = $client->getStoryBySlug('home')->getBody();
// access to the body response...
print_r($data["story"]);
```

### Load Space information

If you need to access some space information like space identifier, space name, the latest version timestamp, or the list of configured languages you can use the `spaces` endpoint.

```php
<?php

require 'vendor/autoload.php';

use Storyblok\Client as StoryblokClient; // you can use also an alias

$client = new StoryblokClient('your-storyblok-readonly-accesstoken');
$space = $client->get('spaces/me/' , $client->getApiParameters());
$data = $space->getBody();
print_r($data);
// Array of the language codes:
print_r($data["space"]["language_codes"]);
// The latest version timestamp:
echo "Last timestamp : " . $data["space"]["version"] . PHP_EOL;
// The space name:
echo "Space name : " . $data["space"]["name"] . PHP_EOL;
// The space id:
echo "Space id : " . $data["space"]["id"] . PHP_EOL;
```

Because the PHP Client, with the current version, doesn't provide an helper for retrieving data from the space endpoint you can use the `get()` method for accessing the `spaces/me` path of the Content Delivery API. The only thing you need to remember is to set the second parameter for the `get()` method injecting the API parameters. Even if you didn't set any parameters, you have to send `getApiParameters()` as the second parameter for the `get()` method because the PHP client manages for you some core parameters like the token. This is because you are using the low-level method `get()`.


### Load a Story by UUID

```php
require 'vendor/autoload.php';
use Storyblok\Client as StoryblokClient; // you can use also an alias
$client = new StoryblokClient('your-storyblok-readonly-accesstoken');
$client->getStoryByUuid('0c092d14-5cd4-477e-922c-c7f8e330aaea');
$data = $client->getBody();
```
The structure of the data returned by the `getBody()` of the `getStoryByUuid()` method, has the same structure of the `getStoryBySlug()` so: `story`, `cv`, `rels`, `links`.



### Load a list of Stories

If you need to retrieve a list of stories you can use the `getStories()` method.
You can use the parameter to filter the stories.
For example, if you want to retrieve all entries from a specific folder you can use `starts_with` option in this way:
```php
$client = new \Storyblok\Client('your-storyblok-readonly-accesstoken');
// Get all Stories from the article folder
$client->getStories(['starts_with' => 'article']);
$data = $client->getBody();
print_r($data["stories"]);
echo $data["cv"] . PHP_EOL;
print_r($data["rels"]);
print_r($data["links"]);
```

Under the hood, the `starts_with` option, filters entries by `full_slug`.

### Load all entries

Because the response from Storyblok API could be paginated, you should walk through all the pages to collect all the entries.
The Storyblok PHP Client provides you a helper named `getAll()` for retrieving all the entries.
Under the hood, the `getAll()` method performs all the API call according to the pagination data (total, per page etc).

Example, retrieving all stories:

```php
$client = new Client('your-storyblok-readonly-accesstoken');
$options = $client->getApiParameters();
$options['per_page'] = 3;
$stories = $client->getAll('stories/', $options);
```

If you want to retrieve the array of the responses for each call:
```php
$client = new Client('your-storyblok-readonly-accesstoken');
$options = $client->getApiParameters();
$options['per_page'] = 3;
$response = $client->getAll('stories/', $options, true);
```





### Load a list of datasource entries
With the `Storyblok\Client` you have also the `getDatasourceEntries()` method for retrieving the list of key/values of the datasource:
```php
$client = new \Storyblok\Client('your-storyblok-readonly-accesstoken');
// Get category entries from datasource
$client->getDatasourceEntries('categories');
// will return as ['name']['value'] Array for easy access
$nameValueArray = $client->getAsNameValueArray();
// instead, if you want to retrieve the whole response, you can use getBody() method:
$data = $client->getBody();
```

If you want to receive also the dimension values besides the default values in one datasource entry you can use the option _dimension_ when you call _getDatasourceEntries()_ method.
You could use dimensions for example when you are using datasource for storing a list of values and you want a translation for the values. In this case, you should create one dimension for each language.

```php
$client = new \Storyblok\Client('your-storyblok-readonly-accesstoken');
// Get product entries with dimension 'de-at'
$client->getDatasourceEntries('products', ['dimension'=> 'de-at']);
// show the dimension values:
foreach ($client->getBody()['datasource_entries'] as $key => $value) {
    echo $value['dimension_value'] . PHP_EOL;
}
```


### Load a list of tags

```php
$client = new \Storyblok\Client('your-storyblok-readonly-accesstoken');
// Get all Tags
$client->getTags();
// will return the whole response
$data = $client->getBody();
// will return as ['tagName1', 'tagName2'] Array for easy access
$stringArray = $client->getAsStringArray();
```

### Access to the Responses Headers

When you perform a request to Content Delivery API, you can access the headers of the HTTP response.
For example, after you call the `getStories()` method (for retrieving a list of stories) you can access to the HTTP response headers via `getHeaders()` method:
```php
$client = new \Storyblok\Client('your-storyblok-readonly-accesstoken');
$result = $client->getStories();
$headersData = $client->getHeaders();
print_r($headersData);
```

### Retrieving Draft or Published content

In a Web application where the query string is available, the content delivery client checks automatically the GET parameters:
- `_storyblok` to get the draft version of a specific story
- `_storyblok_published` to clear the cache.

If you want to override this "default" behavior, or you are in a non-web context (for example you are implementing a command line tool), to retrieve the draft content (for example a not yet published story) you have to use the `editMode()` method.
If you want to retrieve the published content (for example a published story) you have to use the `editMode(false)` method with `false` parameter.
```php
require 'vendor/autoload.php';
use Storyblok\Client as StoryblokClient; // you can use also an alias
$client = new StoryblokClient('your-storyblok-readonly-accesstoken');
$client->editMode(); // forcing draft mode
$data = $client->getStoryBySlug('home')->getBody();
// access to the body response...
print_r($data["story"]);
echo $data["cv"] . PHP_EOL;
print_r($data["rels"]);
print_r($data["links"]);
```



## Managing cache

When you perform an API request you can use the caching mechanism provided by the Storyblok PHP client.
When you initialize the `Storyblok\Client` you can set the cache provider.
For example, using the `setCache()` method you can define the provider (for example filesystem) and an array of options. In case you are using the filesystem as storage of cache items, you can set the path with `path` option:

```php
$client = new \Storyblok\Client('your-storyblok-readonly-accesstoken');
$client->setCache('filesystem', [ 'path' => 'cache']);
$result = $client->getStories();
print_r($result);
```

You can set a TTL value for the cache via `default_lifetime` option.
```php
$client = new \Storyblok\Client('your-storyblok-readonly-accesstoken');
$client->setCache('filesystem',
    [
        'path' => 'cache',
        'default_lifetime' => 3600
    ]);
$result = $client->getStories();
print_r($result);
```


The caching mechanism uses under the hood the Symfony Cache package.
So, you can use the Adapter supported the Symfony Cache.
For example, for using a MySql database as cache storage, you can setup the connection via the PHP PDO class:
```php
$client = new \Storyblok\Client('your-storyblok-readonly-accesstoken');
$pdo = new PDO('mysql:host=127.0.0.1;dbname=db_php-client;charset=utf8mb4;', "root");
$client->setCache('mysql', ['pdo' => $pdo]);
$result = $client->getStories();
print_r($result);
```

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
$client = new \Storyblok\Client('your-storyblok-readonly-accesstoken');
$client->setCache('filesystem', array('path' => 'cache'));
// Flush the whole cache when a story has been published
$client->flushCache();
// Or empty the cache for one specific item only
$client->deleteCacheBySlug('home');
```


## Generate a navigation tree

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

## Nginx SSI - Server Side Includes

Use the following script if you have Nginx SSI enabled and experience issues with printing the _editable html comments directly to manually parse the Storyblok HTML editable comments: https://gist.github.com/DominikAngerer/ca61d41bae3afcc646cfee286579ad36

## Relationships and Links Resolving

In order to resolve relations you can use the `resolveRelations` method of the client passing a comma separated list of fields:

```php
$client = new \Storyblok\Client('your-storyblok-readonly-accesstoken');

$client->resolveRelations('component_name1.field_name1,component_name2.field_name2')
$client->getStoryBySlug('home');
```

Another example:

```php
use Storyblok\Client;
$client = new Client('your-storyblok-readonly-accesstoken');
$client->resolveRelations('popular-articles.articles');
$result = $client->getStoryBySlug("home")->getBody();
```

In order to resolve links, you can use the `resolveLinks` method passing the specific type of resolving you want to perform among `url`, `story` or `link`:

```php
$client = new \Storyblok\Client('your-storyblok-readonly-accesstoken');

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

* Do you have questions about Storyblok or do you need help? [Join our Discord Community](https://discord.gg/jKrbAMz).

### Contributing

Please see our [contributing guidelines](https://github.com/storyblok/.github/blob/master/contributing.md) and our [code of conduct](https://www.storyblok.com/trust-center#code-of-conduct?utm_source=github.com&utm_medium=readme&utm_campaign=php-client).
This project use [semantic-release](https://semantic-release.gitbook.io/semantic-release/) for generate new versions by using commit messages and we use the Angular Convention to naming the commits. Check [this question](https://semantic-release.gitbook.io/semantic-release/support/faq#how-can-i-change-the-type-of-commits-that-trigger-a-release) about it in semantic-release FAQ.

### License

This repository is published under the [MIT](./LICENSE) license.
