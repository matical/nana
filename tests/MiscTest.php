<?php

namespace ksmz\nana\Tests;

use ksmz\nana\Nana;
use ksmz\nana\Fetch;
use GuzzleHttp\Client;

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
        $secondResponse = Nana::get($this->baseUrl . '/ping');

        $firstInstance = Nana::withOptions(['http_errors' => true]);
        $secondOnceResponse = $firstInstance->once();

        $this->assertNotSame($response, $secondResponse);
        $this->assertNotSame($firstInstance, $secondOnceResponse);
    }

    /** @test */
    public function it_returns_a_guzzle_client_when_requested()
    {
        $guzzle = Nana::userAgent('test agent')
                      ->getHttpClient();

        $this->assertInstanceOf(Client::class, $guzzle);
        $this->assertSame($guzzle->getConfig('headers')['User-Agent'], 'test agent');
    }
}
