<?php

namespace ksmz\nana\Tests;

use ksmz\nana\Consume;

class PostTest extends BaseTest
{
    protected function assertPost(array $expected, Consume $actual)
    {
        $this->assertArraySubset($expected, $actual->json(true));
    }

    /** @test */
    public function it_can_send_post_with_json()
    {
        $response = $this->http->asJson()->post('/post', [
            'ksmz' => 'is mine',
            'sck'  => 'bap',
        ]);

        $this->assertPost([
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
    public function it_can_send_post_with_form_params()
    {
        $response = $this->http->asFormParams()->post('/post', [
            'ksmz' => 'is mine',
            'sck'  => 'bap',
        ]);

        $this->assertPost([
            'headers'     => [
                'content-type' => ['application/x-www-form-urlencoded'],
            ],
            'form_params' => [
                'ksmz' => 'is mine',
                'sck'  => 'bap',
            ],
        ], $response);
    }

    /** @test */
    public function it_can_send_post_with_multipart()
    {
        $response = $this->http->asMultipart()->post('/post-multipart', [
            [
                'name'     => 'ksmz',
                'contents' => 'is mine',
            ],
            [
                'name'     => 'testfile',
                'contents' => 'ksmz is mine',
                'filename' => 'ksmz.txt',
            ],
        ])->json(true);

        $this->assertEquals('is mine', $response['field']);
        $this->assertEquals('ksmz.txt', $response['file']['filename']);
        $this->assertEquals('ksmz is mine', $response['file']['content']);
        $this->assertStringStartsWith('multipart/form-data', $response['headers']['content-type'][0]);
    }
}
