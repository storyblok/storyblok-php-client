<?php

use Storyblok\Client;

test('retrieve api key', function () {
    $client = new Client('test');
    $this->assertEquals('test', $client->getApiKey());
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
    $client = mockClient('stories', 'test', 'v1');
    $stories = $client->getStories();

    dd($stories);
});