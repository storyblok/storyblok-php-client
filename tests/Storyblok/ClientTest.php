<?php

use Storyblok\Client;

test('retrieve api key', function () {
    $client = new Client('test');
    $this->assertEquals('test', $client->getApiKey());
});