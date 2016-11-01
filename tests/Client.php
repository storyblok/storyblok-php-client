<?php
require '../vendor/autoload.php';

$client = new \Storyblok\Client('t618GfLe1YHICBioAHnMrwtt', 'localhost:3001');

// Optionally set a cache
$client->setCache('filesytem', array('path' => 'cache'));

// Get the story as array
$client->getStoryBySlug('demo');
$data = $client->getBody();

var_dump($data);

echo '<hr>';

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