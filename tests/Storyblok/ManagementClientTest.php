<?php

use GuzzleHttp\Psr7\Response;
use Storyblok\ManagementClient;

test('can be instanced', function () {
    $this->assertInstanceOf(ManagementClient::class, new ManagementClient('token'));
});