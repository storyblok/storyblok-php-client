<?php

use Storyblok\ApiException;
use Storyblok\Client;

test('can be instanced', function () {
    $this->assertInstanceOf(Client::class, new Client('token'));
});

test('retrieve api parameters', function () {
    $client = (new Client('test'))->getApiParameters();

    $this->assertEquals('test', $client['token']);
    $this->assertEquals('published', $client['version']);
    $this->assertEquals('', $client['cv']);
});

test('story language can be set', function () {
    $client = new Client('test');
    $client->language('en');

    $this->assertEquals('en', $client->getLanguage());
});

test('fallback language can be set', function () {
    $client = new Client('test');
    $client->fallbackLanguage('en');

    $this->assertEquals('en', $client->getFallbackLanguage());
});

test('default content version is set to only published stories', function () {
    $client = new Client('test');

    $this->assertEquals('published', $client->getVersion());
});

test('set content to receive draft versions', function () {
    $client = new Client('test');
    $client->editMode(true);

    $this->assertEquals('draft', $client->getVersion());
});

test('v1: get stories', function () {
    $client = new Client('test', $endpoint = 'stories', $version = 'v1');
    $client->mockable([
        mockResponse($endpoint, [], $version),
    ]);

    $stories = $client->getStories()->getBody();

    $this->assertCount(13, $stories['stories']);
    $this->assertArrayNotHasKey('cv', $stories);
    $this->assertArrayNotHasKey('rels', $stories);
    $this->assertArrayNotHasKey('links', $stories);
});

test('v1: get story by uuid', function () {
    $client = new Client('test', $endpoint = 'storyByUuid', $version = 'v1');
    $client->mockable([
        mockResponse($endpoint, [], $version),
    ]);

    $story = $client->getStoryByUuid('d637be7f-8187-4e8b-9434-93390541f42b')->getBody();

    $this->assertEquals('d637be7f-8187-4e8b-9434-93390541f42b', $story['story']['uuid']);
    $this->assertArrayNotHasKey('cv', $story);
    $this->assertArrayNotHasKey('rels', $story);
    $this->assertArrayNotHasKey('links', $story);
});

test('v2: get stories', function () {
    $client = new Client('test', $endpoint = 'stories', $version = 'v2');
    $client->mockable([
        mockResponse($endpoint, [], $version),
    ]);

    $stories = $client->getStories()->getBody();

    $this->assertCount(13, $stories['stories']);
    $this->assertEquals('Overview', $stories['stories'][0]['name']);
    $this->assertArrayHasKey('cv', $stories);
    $this->assertArrayHasKey('rels', $stories);
    $this->assertArrayHasKey('links', $stories);
});
test('v2: get stories with Cache', function () {
    $client = new Client('test', $endpoint = 'stories', $version = 'v2');
    $client->setCache('filesystem', ['path' => './cache']);
    $client->editMode(false);
    $client->mockable([
        mockResponse($endpoint, ['x-test' => 1], $version),
        mockResponse('stories2', ['x-test' => 2], $version),
    ]);

    $stories = $client->getStories()->getBody();
    $this->assertCount(13, $stories['stories']);
    $this->assertEquals('Overview', $stories['stories'][0]['name']);
    $this->assertArrayHasKey('cv', $stories);
    $this->assertArrayHasKey('rels', $stories);
    $this->assertArrayHasKey('links', $stories);

    $stories = $client->getStories()->getBody();
    $this->assertCount(13, $stories['stories']);
    $this->assertEquals('Overview', $stories['stories'][0]['name']);
    $this->assertArrayHasKey('cv', $stories);
    $this->assertArrayHasKey('rels', $stories);
    $this->assertArrayHasKey('links', $stories);
});

test('v2: get story by uuid', function () {
    $client = new Client('test', $endpoint = 'storyByUuid', $version = 'v2');
    $client->mockable([
        mockResponse($endpoint, [], $version),
    ]);

    $story = $client->getStoryByUuid('d637be7f-8187-4e8b-9434-93390541f42b')->getBody();

    $this->assertEquals('d637be7f-8187-4e8b-9434-93390541f42b', $story['story']['uuid']);
    $this->assertArrayHasKey('cv', $story);
    $this->assertArrayHasKey('rels', $story);
    $this->assertArrayHasKey('links', $story);
});

test('v1: get tags', function () {
    $client = new Client('test', $endpoint = 'tags', $version = 'v1');
    $client->mockable([
        mockResponse($endpoint, [], $version),
    ]);

    $tags = $client->getTags()->getBody();
    expect($tags)->toHaveKey('tags');
    expect($tags['tags'])->toHaveCount(2);
    expect($tags['tags'][0])->toHaveKey('name');
    expect($tags['tags'][0]['name'])->toEqual('red');
    expect($tags['tags'][0])->toHaveKey('taggings_count');
    expect($tags['tags'][0]['taggings_count'])->toEqual(14);
});

test('v1: get tags with 401 error', function () {
    $client = new Client('test', $endpoint = 'tags', $version = 'v1');
    $client->mockable([
        mockResponse($endpoint, [], $version, 401),
        mockResponse($endpoint, [], $version, 401),
    ]);

    try {
        $tags = $client->getTags();
    } catch (ApiException $e) {
        expect($e->getCode())->toEqual(401);
        expect($e->getMessage())->toContain('401 Unauthorize');
    }

    $this->expectException(ApiException::class);
    $tags = $client->getTags();
});
