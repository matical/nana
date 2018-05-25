<?php

namespace ksmz\nana\Tests;

use ksmz\nana\Sink;

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
        $this->assertEquals('test/0.1', $response->json()->headers->{'user-agent'});
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
