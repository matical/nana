<?php

namespace ksmz\nana\Tests;

use ksmz\nana\Consume;

class PutTest extends BaseTest
{
    protected function assertPut(array $expected, Consume $actual)
    {
        $this->assertArraySubset($expected, $actual->json(true));
    }

    /** @test */
    public function it_can_send_put_with_json()
    {
        $response = $this->http->asJson()->put('/put', [
            'ksmz' => 'is mine',
            'sck'  => 'bap',
        ]);

        $this->assertPut([
            'headers'      => [
                'content-type' => 'application/json',
            ],
            'json_payload' => [
                'ksmz' => 'is mine',
                'sck'  => 'bap',
            ],
        ], $response);
    }

    /** @test */
    public function it_can_send_put_with_form_params()
    {
        $response = $this->http->asFormParams()->put('/put', [
            'ksmz' => 'is mine',
            'sck'  => 'bap',
        ]);

        $this->assertPut([
            'headers'     => [
                'content-type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'ksmz' => 'is mine',
                'sck'  => 'bap',
            ],
        ], $response);
    }
}
