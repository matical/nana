<?php

namespace ksmz\nana\Tests;

use ksmz\nana\Sink;

class SinkTest extends BaseTest
{
    /** @test */
    public function clients_can_be_registered()
    {
        $freshInstance = $this->newHttp();
        Sink::registerClient('default', $freshInstance);

        $response = Sink::get('/ping');

        $this->assertEquals($freshInstance, Sink::client('default'));
        $this->assertEquals('pong', $response->getBody());
}

    /** @test */
    public function clients_can_be_registered_via_closures()
    {
        Sink::registerClient('nana', function () {
            return $this->newHttp();
        });

        $response = Sink::client('nana')
                        ->get('/ping');
        $this->assertEquals('pong', $response->getBody());
    }
}
