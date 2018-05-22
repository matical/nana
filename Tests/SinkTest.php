<?php

namespace ksmz\nana\Tests;

use ksmz\nana\Sink;

class SinkTest extends BaseTest
{
    protected function tearDown()
    {
        Sink::$faucets = [];
    }

    /** @test */
    public function clients_can_be_registered_with_an_existing_instance()
    {
        $freshInstance = $this->newHttp();
        Sink::registerClient('default', $freshInstance);

        $response = Sink::get('/ping');

        $this->assertSame($freshInstance, Sink::client('default'));
        $this->assertSame('pong', $response->body());
    }

    /** @test */
    public function clients_can_be_registered_via_closures()
    {
        Sink::registerClient('nana', function () {
            return $this->newHttp();
        });

        $response = Sink::client('nana')
                        ->get('/ping');
        $this->assertSame('pong', $response->body());
    }

    /**
     * @test
     * @expectedException \GuzzleHttp\Exception\RequestException
     * @expectedExceptionMessage <url> malformed
     */
    public function clients_will_register_a_simple_instance()
    {
        Sink::registerClient('fresh');

        Sink::client('fresh')->get('/ping');

        $validResponse = Sink::client('fresh')
                             ->get($this->baseUrl . '/ping');
        $this->assertSame('pong', $validResponse->body());
    }

    /**
     * @test
     * @expectedException \ksmz\nana\Exceptions\ClientAlreadyRegisteredException
     */
    public function clients_cannot_be_registered_twice()
    {
        Sink::registerClient('newClient');
        Sink::registerClient('newClient');
    }
}
