<?php
require '../vendor/autoload.php';

$client = new \Storyblok\Client('Iw3XKcJb6MwkdZEwoQ9BCQtt'); //

// Optionally set a cache
$client->setCache('filesytem', array('path' => 'cache'));

// Get the story as array
$client->getStoryBySlug('home');
$data = $client->getBody();


echo '<link href="https://getuikit.com/css/theme.css?174" rel="stylesheet" type="text/css">';
echo '<div class="uk-container uk-container-center uk-margin-large-top uk-margin-large-bottom">
				<h1> PHP Client Runable Test </h1>
				<h2> getStoryBySlug "home" </h2>
				<pre>';
var_dump($data);
echo '	</pre>';
echo '<hr>';

$tree = $client->editMode()->getLinks()->getAsTree();

echo '<h2> getLinks | getAsTree </h2>
			<ul>';
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

echo '<h2> Check: $client->cacheVersion </h2>
			<pre>';
var_dump($client->cacheVersion);
echo '</pre>
			<hr>
			<p> Wait 2 Seconds </p>';

sleep(2);

echo '<pre>';
var_dump($client->cacheVersion);
echo '</pre>
			<hr>
			<h2> deleteCacheBySlug "home" | flushCache </h2>';

$client->deleteCacheBySlug('home');

echo '<pre>';
var_dump($client->cacheVersion);
echo '</pre>';

$client->flushCache();

echo '<hr>';

$client->getDatasourceEntries('test-case-1');
$data = $client->getBody();

echo '<h2> getDatasourceEntries "test-case-1" | getBody </h2>
			<pre>';
var_dump($data);
echo '</pre>';
echo '<hr>';

$data = $client->getAsNameValueArray();

echo '<h2> getDatasourceEntries "test-case-1" | getAsNameValueArray </h2>
			<pre>';
var_dump($data);
echo '</pre>';
echo '<hr>';

echo '</div>';