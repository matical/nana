<?php

namespace ksmz\nana\Tests;

use ksmz\nana\Consume;

class DeleteTest extends BaseTest
{
    protected function assertDelete(array $expected, Consume $actual)
    {
        $this->assertArraySubset($expected, $actual->json(true));
    }

    /** @test */
    public function it_can_send_delete_with_json()
    {
        $response = $this->http->asJson()->delete('/delete', [
            'ksmz' => 'is mine',
            'sck'  => 'bap',
        ]);

        $this->assertDelete([
            'headers'      => [
                'content-type' => ['application/json'],
            ],
            'json_payload' => [
                'ksmz' => 'is mine',
                'sck'  => 'bap',
            ],
        ], $response);
    }

    /** @test */
    public function it_can_send_delete_with_form_params()
    {
        $response = $this->http->asFormParams()->delete('/delete', [
            'ksmz' => 'is mine',
            'sck'  => 'bap',
        ]);

        $this->assertDelete([
            'headers'     => [
                'content-type' => ['application/x-www-form-urlencoded'],
            ],
            'form_params' => [
                'ksmz' => 'is mine',
                'sck'  => 'bap',
            ],
        ], $response);
    }
}
