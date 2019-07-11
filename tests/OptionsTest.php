<?php

namespace ksmz\nana\Tests;

use ksmz\nana\Consume;

class OptionsTest extends BaseTest
{
    protected function assertHeaders(array $expected, Consume $actual)
    {
        $this->assertArraySubset(['headers' => $expected], $actual->json(true));
    }

    protected function assertHeader($header, $expected, Consume $actual)
    {
        $this->assertArraySubset([
            $header => $expected,
        ], $actual->json(true)['headers']);
    }

    /** @test */
    public function it_allows_headers_to_be_set()
    {
        $response = $this->http->withHeaders(['X-ksmz' => 'is-mine'])
                               ->get('/get');

        $this->assertHeader('x-ksmz', 'is-mine', $response);
    }

    /** @test */
    public function it_allows_custom_user_agents()
    {
        $response = $this->http->userAgent('kwsm/0.1')
                               ->get('/get');

        $this->assertHeader('user-agent', 'kwsm/0.1', $response);
    }

    /** @test */
    public function it_allows_accept_to_be_specified()
    {
        $response = $this->http->accepts('application/x.shiraishi.v1+json')
                               ->get('/get');

        $this->assertHeader('accept', 'application/x.shiraishi.v1+json', $response);
    }

    /** @test */
    public function it_allows_raw_options_to_be_passed()
    {
        $response = $this->http->withOptions([
            'headers' => [
                'X-ksmz' => 'is mine',
                'custom' => 'header',
            ],
        ])->get('/get');

        $this->assertHeaders([
            'x-ksmz' => 'is mine',
            'custom' => 'header',
        ], $response);
    }
}
