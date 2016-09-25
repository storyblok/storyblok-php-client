# About
This is the Storyblok php client for easy access of the publishing api.

The library checks the get parameters _storyblok to get the draft version of a specific story and _storyblok_published to clear the cache.

## Install

```bash
composer require storyblok/php-client dev-master
```

## Usage

```php
// Require composer autoload
require 'vendor/autoload.php';

// Initialize
$client = new \Storyblok\Client('your-storyblok-private-token');
$client->setSpace('your-space-id');

// Optionally set a cache
$client->setCache('filesytem', array('path' => 'cache'));

// Get the story as array
$client->getStoryBySlug('home');
$data = $client->getBody();
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
