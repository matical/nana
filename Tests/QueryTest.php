<?php

namespace ksmz\nana\Tests;

use ksmz\nana\Consume;

class QueryTest extends BaseTest
{
    protected function assertQuery(array $expected, Consume $actual)
    {
        $this->assertArraySubset([
            'query_strings' => $expected,
        ], $actual->json(true));
    }

    /** @test */
    public function it_works()
    {
        $response = $this->http->get('/ping');
        $this->assertSame('pong', $response->body());
    }

    /** @test */
    public function it_acknowledges_query_strings_in_urls()
    {
        $response = $this->http->get('/get?ksmz=is%20mine&sck=bap');

        $this->assertQuery([
            'ksmz' => 'is mine',
            'sck'  => 'bap',
        ], $response);
    }

    /** @test */
    public function it_acknowledges_query_strings_via_arrays()
    {
        $response = $this->http->get('/get', [
            'ksmz' => 'is mine',
            'sck'  => 'bap',
        ]);

        $this->assertQuery([
            'ksmz' => 'is mine',
            'sck'  => 'bap',
        ], $response);
    }

    /** @test */
    public function it_allows_query_params_to_be_combined_with_parameters()
    {
        $response = $this->http->get('/get?ksmz=is%20mine&sck=bap', [
            'bapbap' => 'bap',
        ]);

        $this->assertQuery([
            'ksmz'   => 'is mine',
            'sck'    => 'bap',
            'bapbap' => 'bap',
        ], $response);
    }
}
