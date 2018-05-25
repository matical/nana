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
        Sink::registerFaucet('default', $freshInstance);

        $response = Sink::get('/ping');

        $this->assertSame($freshInstance, Sink::faucet('default'));
        $this->assertSame('pong', $response->body());
    }

    /** @test */
    public function clients_can_be_registered_via_closures()
    {
        Sink::registerFaucet('nana', function () {
            return $this->newHttp();
        });

        $response = Sink::faucet('nana')
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
        Sink::registerFaucet('fresh');

        Sink::faucet('fresh')->get('/ping');

        $validResponse = Sink::faucet('fresh')
                             ->get($this->baseUrl . '/ping');
        $this->assertSame('pong', $validResponse->body());
    }

    /**
     * @test
     * @expectedException \ksmz\nana\Exceptions\ClientAlreadyRegisteredException
     */
    public function clients_cannot_be_registered_twice()
    {
        Sink::registerFaucet('newClient');
        Sink::registerFaucet('newClient');
    }

    /**
     * @test
     * @expectedException \ksmz\nana\Exceptions\NonExistentClientException
     */
    public function throws_exception_when_default_sink_is_not_present()
    {
        Sink::get('/get');
    }
}
