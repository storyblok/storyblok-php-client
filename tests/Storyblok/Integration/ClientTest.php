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

test('Integration: check casting after enriching content', function () {
    $client = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $client->editMode();
    $options = $client->getApiParameters();
    $response = $client->getStoryBySlug('home');
    $body = $response->getBody();
    expect($body)->toBeArray()
        ->toHaveKey('story')
        ->toHaveCount(4)
    ;
    $story = $body['story'];
    expect($story)->toBeArray()
        ->toHaveKey('content')
        ->toHaveCount(22)
    ;
    $content = $story['content'];
    expect($content)->toBeArray()
        ->toHaveKey('_uid')
        ->toHaveKey('body')
        ->toHaveKey('component')
        ->toHaveCount(4)
    ;
    expect($content['_uid'])->toBeString();
    expect($content['component'])->toBeString();
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

test('Integration: get one story with Resolved relations 1', function () {
    unset($_GET['_storyblok_published']);
    $client = new Client('HMqBPn2a92FjXYI3tQGDVQtt');
    $slug = 'home';
    $key = 'stories/' . $slug;
    $client->editMode();
    $options = $client->getApiParameters();
    $client->resolveRelations(
        'page.RelatedProducts,page.MainProduct,page.FeaturedCategoryProducts,ProductCategory.Products,Product.ProductVariants'
    );
    $responses = $client->getStoryBySlug($slug);
    $body = $responses->getBody();
    expect($body)->toHaveKeys(['rel_uuids', 'story', 'cv', 'links']);
    expect($body['story']['content']['FeaturedCategoryProducts'])->toBeArray();
    expect($body['story']['content']['FeaturedCategoryProducts']['0']['name'])->toEqual('Category 001');
    expect($body['story']['content']['FeaturedCategoryProducts']['1']['name'])->toEqual('Category A');
    expect($body['story']['content']['FeaturedCategoryProducts']['2']['name'])->toEqual('Category B');
    expect($body['story']['content']['FeaturedCategoryProducts'])->toHaveLength(3);
    expect($body['story']['content']['MainProduct'])->toBeArray();
    expect($body['story']['content']['MainProduct']['name'])->toEqual('Bike 001');
    expect($body['story']['content']['MainProduct']['content']['ProductVariants'])->toBeArray()->toHaveLength(2);
    expect($body['rel_uuids'])->toHaveLength(51);
})->group('integration');

test('Integration: get one story with few resolved relations', function () {
    unset($_GET['_storyblok_published']);
    $client = new Client('HMqBPn2a92FjXYI3tQGDVQtt');
    $slug = 'categories/category-shoe-001';
    $key = 'stories/' . $slug;
    $client->editMode();
    $options = $client->getApiParameters();
    $client->resolveRelations(
        'ProductCategory.Products,Product.ProductVariants'
    );
    $responses = $client->getStoryBySlug($slug);
    $body = $responses->getBody();
    expect($body)->toHaveKeys(['rels', 'story', 'cv', 'links']);
    expect($body['story']['content']['Products'])->toBeArray();
    expect($body['story']['content']['Products'])->toHaveLength(1);
    expect($body['story']['content']['Products'][0]['content']['productname'])->toEqual('Shoe 001');
    expect($body['story']['content']['Products'][0]['content']['ProductVariants'])->toBeArray()->toHaveLength(3);
    expect($body['story']['content']['Products'][0]['content']['ProductVariants']['0'])->toBeArray();
    expect($body['story']['content']['Products'][0]['content']['ProductVariants']['0']['name'])->toEqual('Shoe 001 Blue');
    expect($body['story']['content']['Products'][0]['content']['ProductVariants']['0']['content'])->toBeArray();
    expect($body['story']['content']['Products'][0]['content']['ProductVariants']['0']['content']['VariantName'])->toEqual('Shoe 001 Blue');
    expect($body['rels'])->toHaveLength(4);
})->group('integration');

test('Integration: get one story from Product', function () {
    unset($_GET['_storyblok_published']);
    $client = new Client('HMqBPn2a92FjXYI3tQGDVQtt');
    $slug = 'categories/products/bike-001';
    $key = 'stories/' . $slug;
    $client->editMode();
    $options = $client->getApiParameters();
    $client->resolveRelations(
        'Product.ProductVariants'
    );
    $responses = $client->getStoryBySlug($slug);
    $body = $responses->getBody();
    expect($body)->toHaveKeys(['rels', 'story', 'cv', 'links']);
    // print_r($body);
    expect($body['story']['content']['productname'])->toEqual('Bike 001');
    expect($body['story']['content']['ProductVariants'])->toBeArray();
    expect($body['story']['content']['ProductVariants'])->toHaveLength(2);
    expect($body['story']['content']['ProductVariants']['0']['name'])->toBeString();
    expect($body['story']['content']['ProductVariants']['0']['name'])->toEqual('Bike 001 L');
    expect($body['story']['content']['ProductVariants']['0']['content']['VariantName'])->toEqual('Bike 001 L');
})->group('integration');

test('Integration: get one story with Resolved relations 2', function () {
    unset($_GET['_storyblok_published']);
    $client = new Client('HMqBPn2a92FjXYI3tQGDVQtt');
    $slug = 'home';
    $key = 'stories/' . $slug;
    $client->editMode();
    $options = $client->getApiParameters();
    $client->resolveRelations(
        'page.MainProduct,ProductCategory.Products,Product.ProductVariants'
    );
    $responses = $client->getStoryBySlug($slug);
    $body = $responses->getBody();
    expect($body)->toHaveKeys(['rels', 'story', 'cv', 'links']);
    expect($body['story']['content']['FeaturedCategoryProducts'])->toBeArray();
    expect($body['story']['content']['FeaturedCategoryProducts'])->toHaveLength(3);
    expect($body['story']['content']['FeaturedCategoryProducts']['0'])->toBeString();
    expect($body['story']['content']['FeaturedCategoryProducts']['1'])->toBeString();
    expect($body['story']['content']['FeaturedCategoryProducts']['2'])->toBeString();
    expect($body['story']['content']['MainProduct'])->toBeArray();
    expect($body['story']['content']['MainProduct']['name'])->toEqual('Bike 001');
    expect($body['story']['content']['MainProduct']['content']['ProductVariants'])->toBeArray()->toHaveLength(2);
    expect($body['story']['content']['MainProduct']['content']['ProductVariants']['0']['content']['VariantName'])->toEqual('Bike 001 L');
})->group('integration');

test('Integration: get list of stories story with Resolved relations 2', function () {
    unset($_GET['_storyblok_published']);
    $client = new Client('HMqBPn2a92FjXYI3tQGDVQtt');

    $key = 'stories/';
    $params = [
        'starts_with' => 'categories/category-shoe',
        'content_type' => 'ProductCategory',
    ];
    $client->editMode();
    $options = $client->getApiParameters();
    $client->resolveRelations(
        'ProductCategory.Products,Product.ProductVariants'
    );
    $responses = $client->getStories($params);
    $body = $responses->getBody();
    expect($body)->toHaveKeys(['rels', 'stories', 'cv', 'links']);
    expect($body['stories'][0]['name'])->toEqual('Category Shoe 001');

    expect($body['stories'][0]['content']['Products'])->toBeArray();
    expect($body['stories'][0]['content']['Products'])->toHaveLength(1);
    expect($body['stories'][0]['content']['Products'][0]['name'])->toEqual('Shoe 001');
    expect($body['stories'][0]['content']['Products'][0]['content']['ProductVariants'])->toBeArray()->toHaveLength(3);
    expect($body['stories'][0]['content']['Products'][0]['content']['ProductVariants']['0']['content']['VariantName'])->toEqual('Shoe 001 Blue');
})->group('integration');

test('Integration: get list of stories -translations- story with Resolved relations 2', function () {
    unset($_GET['_storyblok_published']);
    $client = new Client('HMqBPn2a92FjXYI3tQGDVQtt');

    $key = 'stories/';
    $params = [
        'starts_with' => 'categories/category-shoe',
        'content_type' => 'ProductCategory',
    ];
    $client->editMode();
    $client->language('it');
    $options = $client->getApiParameters();
    $client->resolveRelations(
        'ProductCategory.Products,Product.ProductVariants'
    );
    $responses = $client->getStories($params);
    $body = $responses->getBody();
    expect($body)->toHaveKeys(['rels', 'stories', 'cv', 'links']);
    expect($body['stories'][0]['name'])->toEqual('Category Shoe 001');

    expect($body['stories'][0]['content']['Products'])->toBeArray();
    expect($body['stories'][0]['content']['Products'])->toHaveLength(1);
    expect($body['stories'][0]['content']['Products'][0]['name'])->toEqual('Shoe 001');
    expect($body['stories'][0]['content']['Products'][0]['content']['ProductVariants'])->toBeArray()->toHaveLength(3);
    expect($body['stories'][0]['content']['Products'][0]['content']['ProductVariants']['0']['content']['VariantName'])->toEqual('Scarpa 001 Blu');
})->group('integration');

test('Integration: get one story with Resolved relations 3', function () {
    unset($_GET['_storyblok_published']);
    $client = new Client('HMqBPn2a92FjXYI3tQGDVQtt');
    $slug = 'categories/products/bike-001';
    $key = 'stories/' . $slug;
    $client->editMode();
    $options = $client->getApiParameters();
    $client->resolveRelations(
        'Product.ProductVariants'
    );
    $responses = $client->getStoryBySlug($slug);
    $body = $responses->getBody();
    expect($body)->toHaveKeys(['rels', 'story', 'cv', 'links']);
    expect($body['story']['content']['ProductVariants'])->toBeArray();
    expect($body['story']['content']['ProductVariants'])->toHaveLength(2);
    expect($body['story']['content']['ProductVariants']['0']['name'])->toBeString();
    expect($body['story']['content']['ProductVariants']['1']['name'])->toBeString();
})->group('integration');

test('Integration: test stop resolving loop', function () {
    unset($_GET['_storyblok_published']);
    $client = new Client('HMqBPn2a92FjXYI3tQGDVQtt');
    $slug = 'testlevel';
    $key = 'stories/' . $slug;
    $client->editMode();
    $options = $client->getApiParameters();
    $client->resolveRelations(
        'level-1.related,level-2.related'
    );
    $responses = $client->getStoryBySlug($slug);
    $body = $responses->getBody();

    expect($body)->toHaveKeys(['rels', 'story', 'cv', 'links']);
    expect($body['story']['content']['related'])->toBeArray();
    expect($body['story']['content']['related']['name'])->toEqual('TestLevel2');
    expect($body['story']['content']['related']['_stopResolving'])->toEqual(1);
    expect($body['story']['content']['related']['content']['related']['name'])->toEqual('TestLevel');
    expect($body['story']['content']['related']['content']['related']['content']['related'])->toBeArray();
    expect($body['story']['content']['related']['content']['related']['content']['related']['content']['related'])->toBeString();
})->group('integration');
