<?php

namespace ksmz\nana\Tests;

use ksmz\nana\Fetch;
use ksmz\nana\Sink;

class SinkTest extends BaseTest
{
    public function testRegisterClient()
    {
        Sink::registerClient('default', function () {
            return new Fetch(['base_uri' => 'http://localhost:8888']);
        });
    }
}
