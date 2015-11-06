```
$client = new \Storyblok\Storyblok('localhost:3001');
#$client->setCachePath(ROOT_DIR . '/cache');
$client->setSpace('3');
$client->getStoryBySlug($slug);
$data = $client->getStoryContent();
$data = json_decode(json_encode($data), true);
```