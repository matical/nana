<?php

namespace ksmz\nana\Tests;

use ksmz\nana\Consume;

class PatchTest extends BaseTest
{
    protected function assertPatch(array $expected, Consume $actual)
    {
        $this->assertArraySubset($expected, $actual->json(true));
    }

    /** @test */
    public function it_can_send_patch_with_json()
    {
        $response = $this->http->asJson()->patch('/patch', [
            'ksmz' => 'is mine',
            'sck'  => 'bap',
        ]);

        $this->assertPatch([
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
    public function it_can_send_patch_with_form_params()
    {
        $response = $this->http->asFormParams()->patch('/patch', [
            'ksmz' => 'is mine',
            'sck'  => 'bap',
        ]);

        $this->assertPatch([
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
