<?php

use Storyblok\Client;

test('Integration: get space', function () {
    $client = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $space = $client->get('spaces/me/', $client->getApiParameters());
    $this->assertArrayHasKey('space', $space->httpResponseBody);
    $this->assertCount(5, $space->httpResponseBody['space']);
    $this->assertEquals('40101', $space->httpResponseBody['space']['id']);
})->setGroups(['integration']);

test('Integration: get All stories', function () {
    $client = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $options = $client->getApiParameters();
    $options['per_page'] = 3;
    $stories = $client->getAll('stories/', $options);
    $this->assertCount(8, $stories);
})->setGroups(['integration']);

test('Integration: get All stories with default pagination', function () {
    $client = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $options = $client->getApiParameters();
    $stories = $client->getAll('stories/', $options);
    $this->assertCount(8, $stories);
})->setGroups(['integration']);

test('Integration: get All responses stories', function () {
    $client = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $options = $client->getApiParameters();
    $options['per_page'] = 3;
    $responses = $client->getAll('stories/', $options, true);
    $this->assertCount(3, $responses);
})->setGroups(['integration']);

test('Integration: get All responses stories with default pagination', function () {
    $client = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $options = $client->getApiParameters();
    $responses = $client->getAll('stories/', $options, true);
    $this->assertCount(1, $responses);
})->setGroups(['integration']);
