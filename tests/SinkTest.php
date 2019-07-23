<?php

namespace ksmz\nana\Tests;

use ksmz\nana\Sink;
use ksmz\nana\Fetch;

class SinkTest extends BaseTest
{
    protected function tearDown()
    {
        Sink::$faucets = [];
        Sink::$configs = [];
    }

    /** @test */
    public function clients_can_be_registered()
    {
        Sink::register('default', [
            'http_errors' => false,
            'headers'     => [
                'User-Agent' => 'test/0.1',
            ],
        ]);

        $response = Sink::get($this->baseUrl . '/get');
        $this->assertSame('test/0.1', $response->json()->headers->{'user-agent'});
    }

    /** @test */
    public function existing_instances_can_be_registered()
    {
        $existingInstance = (new Fetch())->httpErrors(false)
                                         ->userAgent('test/0.1');

        Sink::register('default', $existingInstance);
        $storedInstance = Sink::faucet('default');

        $this->assertSame($existingInstance, $storedInstance);
    }

    /**
     * @test
     * @expectedException \ksmz\nana\Exceptions\ClientAlreadyRegisteredException
     */
    public function clients_cannot_be_registered_twice()
    {
        Sink::register('default', ['http_errors' => false]);
        Sink::register('default', ['http_errors' => true]);
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
