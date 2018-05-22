<?php

namespace ksmz\nana\Tests;

use ksmz\nana\Consume;

class ResponseTest extends BaseTest
{
    /** @test */
    public function it_is_macroable()
    {
        Consume::macro('isTeapot', function () {
            return $this->status() === 418;
        });

        $trueResponse = $this->newHttp()
                             ->withOptions(['http_errors' => false])
                             ->get('/status/418');

        $falseResponse = $this->newHttp()
                              ->withOptions(['http_errors' => false])
                              ->get('/status/420');

        $this->assertTrue($trueResponse->isTeapot());
        $this->assertFalse($falseResponse->isTeapot());
    }

    /** @test */
    public function it_proxies_response_calls()
    {
        $response = $this->http->get('/status/206');

        $this->assertSame(206, $response->getStatusCode());
    }

    /** @test */
    public function it_fetches_headers()
    {
        $response = $this->http->get('/header/ksmz/mine');

        $this->assertSame('mine', $response->header('ksmz'));
    }

    /** @test */
    public function it_casts_bodies()
    {
        $response = $this->http->get('/ping');

        $this->assertSame('pong', (string) $response);
    }

    /** @test */
    public function it_follows_redirects()
    {
        $response = $this->http->get('/from');

        $this->assertSame(200, $response->status());
        $this->assertSame('redirected', $response->body());
    }

    public function testStatusOkay()
    {
        $response = $this->http->withOptions(['http_errors' => false])
                               ->get('/status/200');

        $this->assertTrue($response->isOk());
        $this->assertNotTrue($response->isRedirection());
        $this->assertNotTrue($response->isClientError());
        $this->assertNotTrue($response->isServerError());
    }

    public function testStatusRedirected()
    {
        $response = $this->http->withOptions(['http_errors' => false])
                               ->get('/status/300');

        $this->assertNotTrue($response->isOk());
        $this->assertTrue($response->isRedirection());
        $this->assertNotTrue($response->isClientError());
        $this->assertNotTrue($response->isServerError());
    }

    public function testStatusClientError()
    {
        $response = $this->http->withOptions(['http_errors' => false])
                               ->get('/status/400');

        $this->assertNotTrue($response->isOk());
        $this->assertNotTrue($response->isRedirection());
        $this->assertTrue($response->isClientError());
        $this->assertNotTrue($response->isServerError());
    }

    public function testStatusServerError()
    {
        $response = $this->http->withOptions(['http_errors' => false])
                               ->get('/status/500');

        $this->assertNotTrue($response->isOk());
        $this->assertNotTrue($response->isRedirection());
        $this->assertNotTrue($response->isClientError());
        $this->assertTrue($response->isServerError());
    }
}
