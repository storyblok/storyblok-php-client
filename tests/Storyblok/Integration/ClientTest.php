<?php

use Storyblok\Client;

test('Integration: get space', function () {
    $client = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $space = $client->get('spaces/me/', $client->getApiParameters());
    $this->assertArrayHasKey('space', $space->httpResponseBody);
    $this->assertCount(5, $space->httpResponseBody['space']);
    $this->assertEquals('40101', $space->httpResponseBody['space']['id']);
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
/*
test('Integration: get one story with cache', function () {
    $client = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $options = $client->getApiParameters();
    $client->setCache('filesystem', ['path' => 'cache']);
    $_GET['_storyblok_published'] = 129972584;
    $responses = $client->getStoryBySlug('home');
    $body = $responses->getBody();
    $this->assertArrayHasKey('story', $body);
    $this->assertArrayHasKey('name', $body['story']);
    $this->assertEquals('home', $body['story']['name']);
})->group('integration');
*/

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

test('Integration: check casting after enriching content', function () {
    $client = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $client->editMode();
    $options = $client->getApiParameters();
    $response = $client->getStoryBySlug("home");
    $body = $response->getBody();
    expect($body)->toBeArray()
    ->toHaveKey("story")
        ->toHaveCount(4);
    $story = $body["story"];
    expect($story)->toBeArray()
        ->toHaveKey("content")
        ->toHaveCount(22);
    $content = $story["content"];
    expect($content)->toBeArray()
        ->toHaveKey("_uid")
        ->toHaveKey("body")
        ->toHaveKey("component")
        ->toHaveCount(4);
    expect($content["_uid"])->toBeString();
    expect($content["component"])->toBeString();


})->group('integration');
