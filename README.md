# About
This is the Storyblok php client for accessing the publishing api

## Install
```bash
composer require storyblok/php-client
```

## Usage

```php
// Initialize
$client = new \Storyblok\Client('your-storyblok-private-token');
$client->setSpace('your-space-id');

// Optionally set a cache
$client->setCache('filesytem', $app['config.cacheFolder']);

// Get the story as array
$client->getStoryBySlug('home');
$data = $client->getStoryContent();
```

### License

This project is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
