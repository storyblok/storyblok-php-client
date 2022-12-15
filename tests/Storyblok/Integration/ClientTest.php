<?php

use Storyblok\Client;

test('Integration: get stories', function () {
    $client = new Client('Iw3XKcJb6MwkdZEwoQ9BCQtt');
    $space = $client->get('spaces/me/', $client->getApiParameters());
    $this->assertArrayHasKey('space', $space->httpResponseBody);
    $this->assertCount(5, $space->httpResponseBody['space']);
    $this->assertEquals('40101', $space->httpResponseBody['space']['id']);
})->setGroups(['integration']);
