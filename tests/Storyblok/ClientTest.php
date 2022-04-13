<?php

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
    $client->editMode(enabled: true);

    $this->assertEquals('draft', $client->getVersion());
});

test('v1: get stories', function () {
    $client = new Client('test', $endpoint = 'stories', $version = 'v1');
    $client->mockable([
        mockResponse(endpoint: $endpoint, version: $version)
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
        mockResponse(endpoint: $endpoint, version: $version)
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
        mockResponse(endpoint: $endpoint, version: $version)
    ]);
    
    $stories = $client->getStories()->getBody();

    $this->assertCount(13, $stories['stories']);
    $this->assertArrayHasKey('cv', $stories);
    $this->assertArrayHasKey('rels', $stories);
    $this->assertArrayHasKey('links', $stories);
});

test('v2: get story by uuid', function () {
    $client = new Client('test', $endpoint = 'storyByUuid', $version = 'v2');
    $client->mockable([
        mockResponse(endpoint: $endpoint, version: $version)
    ]);
    
    $story = $client->getStoryByUuid('d637be7f-8187-4e8b-9434-93390541f42b')->getBody();

    $this->assertEquals('d637be7f-8187-4e8b-9434-93390541f42b', $story['story']['uuid']); 
    $this->assertArrayHasKey('cv', $story);
    $this->assertArrayHasKey('rels', $story);
    $this->assertArrayHasKey('links', $story);   
});