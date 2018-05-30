<?php

namespace ksmz\nana\Tests;

use ksmz\nana\Nana;
use ksmz\nana\Fetch;
use ksmz\nana\Consume;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Responsable;

class MiscTest extends BaseTest
{
    /** @test */
    public function it_creates_unique_classes()
    {
        $this->assertNotSame(Fetch::newInstance(), Fetch::newInstance());
    }

    /**
     * @test
     * @expectedException \GuzzleHttp\Exception\GuzzleException
     */
    public function it_rethrows_guzzle_exceptions()
    {
        Fetch::newInstance()->get('invaliduri');
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

    /** @test */
    public function implements_laravel_interfaces()
    {
        $prophecy = $this->prophesize(Consume::class)
                         ->reveal();

        $this->assertInstanceOf(Jsonable::class, $prophecy);
        $this->assertInstanceOf(Responsable::class, $prophecy);
    }
}
