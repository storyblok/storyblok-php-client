<?php

use Storyblok\Client;

test('Integration: get space', function () {
    $client = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $space = $client->get('spaces/me/', $client->getApiParameters());
    $this->assertArrayHasKey('space', $space->httpResponseBody);
    $this->assertCount(5, $space->httpResponseBody['space']);
    $this->assertEquals('40101', $space->httpResponseBody['space']['id']);
})->group('integration');

test('Integration: get links', function () {
    $client = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $client->editMode(false);
    $links = $client->get('links', $client->getApiParameters());
    $this->assertArrayHasKey('links', $links->httpResponseBody);
    $this->assertCount(11, $links->httpResponseBody['links']);
})->group('integration');

test('Integration: get All stories', function () {
    $client = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $options = $client->getApiParameters();
    $options['per_page'] = 3;
    $stories = $client->getAll('stories/', $options);
    $this->assertCount(8, $stories);
})->group('integration');

test('Integration: get All stories with default pagination', function () {
    $client = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $options = $client->getApiParameters();
    $stories = $client->getAll('stories/', $options);
    $this->assertCount(8, $stories);
})->group('integration');

test('Integration: get All responses stories', function () {
    $client = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $options = $client->getApiParameters();
    $options['per_page'] = 3;
    $responses = $client->getAll('stories/', $options, true);
    $this->assertCount(3, $responses);
})->group('integration');

// useful for testing and reproduce the issue: https://github.com/storyblok/php-client/issues/54
test('Integration: get one story with _storyblok_published', function () {
    $client = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $options = $client->getApiParameters();
    $_GET['_storyblok_published'] = 129972584;
    $responses = $client->getStoryBySlug('home');
    $body = $responses->getBody();
    $this->assertArrayHasKey('story', $body);
    $this->assertArrayHasKey('name', $body['story']);
    $this->assertEquals('home', $body['story']['name']);
})->group('integration');

test('Integration: get All responses stories with default pagination', function () {
    $client = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $options = $client->getApiParameters();
    $responses = $client->getAll('stories/', $options, true);
    $this->assertCount(1, $responses);
})->group('integration');

test('Integration: get one story with option with cache', function () {
    unset($_GET['_storyblok_published']);
    $client = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $client->setCache('filesystem', ['path' => 'cache']);
    $slug = 'home';
    $key = 'stories/' . $slug;
    $cacheKey = hash('sha256', $key);
    $cachedItem = $client->getCachedItem($cacheKey);
    expect($cachedItem->isHit())->toBeBool()->toBeFalse();
    $options = $client->getApiParameters();
    $client->resolveLinks('url');
    $responses = $client->getStoryBySlug($slug);
    $body = $responses->getBody();
    $cv = $responses->getCacheVersion();
    $this->assertArrayHasKey('story', $body);
    $this->assertArrayHasKey('name', $body['story']);
    $this->assertEquals('home', $body['story']['name']);
    $this->assertArrayHasKey('cv', $body);
    expect($body['cv'])->toBeNumeric();
    expect($cv)->toBeNumeric();
    $key = 'stories/' . $slug;
    $cacheKey = hash('sha256', $key);
    $cachedItem = $client->getCachedItem($cacheKey);
    expect($cachedItem->isHit())->toBeBool()->toBeTrue();
    $cv1 = $responses->getBody()['cv'];
    $client2 = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $client2->setCache('filesystem', ['path' => 'cache']);
    $responses2 = $client2->getStoryBySlug($slug);
    $cv2 = $responses2->getBody()['cv'];
    expect($cv1)->toEqual($cv2);
    $identifier2 = $responses2->getHeaders()['X-Request-Id'];
    $identifier1 = $responses->getHeaders()['X-Request-Id'];
    expect($identifier1[0])->toEqual($identifier2[0]);
    $_GET['_storyblok_published'] = 129972584;
    $client3 = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $client3->setCache('filesystem', ['path' => 'cache']);
    $responses3 = $client3->getStoryBySlug($slug);
    $cv3 = $responses3->getBody()['cv'];
    expect($cv1)->toEqual($cv3);
    $identifier3 = $responses3->getHeaders()['X-Request-Id'];
    $identifier1 = $responses->getHeaders()['X-Request-Id'];
    expect($identifier1[0])->not()->toEqual($identifier3[0]);
})->group('integration');
