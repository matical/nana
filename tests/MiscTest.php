<?php

namespace ksmz\nana\Tests;

use ksmz\nana\Nana;
use ksmz\nana\Fetch;

class MiscTest extends BaseTest
{
    /** @test */
    public function it_creates_unique_classes()
    {
        $this->assertNotSame(Fetch::new(), Fetch::new());
    }

    /**
     * @test
     * @expectedException \GuzzleHttp\Exception\GuzzleException
     */
    public function it_rethrows_guzzle_exceptions()
    {
        Fetch::new()->get('invaliduri');
    }

    /**
     * @test
     * @expectedException \GuzzleHttp\Exception\RequestException
     */
    public function it_rethrows_guzzle_http_exceptions()
    {
        $this->http->get('nonExistentRoute');
    }

    /** @test */
    public function nana_proxies_and_creates_new_instances()
    {
        $response = Nana::get($this->baseUrl . '/ping');

        $this->assertSame('pong', $response->body());
    }
}