# About
This is the Storyblok php client for easy access of the publishing api.

The library checks the get parameters _storyblok to get the draft version of a specific story and _storyblok_published to clear the cache.

## Install

```bash
composer require storyblok/php-client dev-master
```

## Usage

### Load a Story

```php
// Require composer autoload
require 'vendor/autoload.php';

// Initialize
$client = new \Storyblok\Client('your-storyblok-private-token');

// Optionally set a cache
$client->setCache('filesytem', array('path' => 'cache'));

// Get the story as array
$client->getStoryBySlug('home');
$data = $client->getBody();
```

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

// Get all Stories that start with news
$client->getDatasourceEntries('categories');

// will return the whole response
$data = $client->getBody();

// will return as ['name']['value'] Array for easy access
$nameValueArray = $client->getAsNameValueArray();

```

### Load a list of tags

```php
// Require composer autoload
require 'vendor/autoload.php';

// Initialize
$client = new \Storyblok\Client('your-storyblok-private-token');

// Optionally set a cache
$client->setCache('filesytem', array('path' => 'cache'));

// Get all Stories that start with news
$client->getTags();

// will return the whole response
$data = $client->getBody();

// will return as ['tagName1', 'tagName2'] Array for easy access
$stringArray = $client->getAsStringArray();

```

## Clearing the cache (Optionally if using setCache)

In order to flush the cache when the user clicks publish, you need to listen to the published event in javascript or define a webhook in the space settings that clears the cache on your server.

```html
<script type="text/javascript" src="//app.storyblok.com/storyblok-latest.js"></script>
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
$client->setCache('filesytem', array('path' => 'cache'));

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

### License

This project is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
