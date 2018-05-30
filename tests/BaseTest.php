<?php

namespace ksmz\nana\Tests;

use ksmz\nana\Fetch;
use PHPUnit\Framework\TestCase;
use ksmz\nana\Tests\Fixtures\Server;

abstract class BaseTest extends TestCase
{
    /**
     * @var string
     */
    protected $baseUrl = 'http://localhost:8888';

    /**
     * @var Fetch
     */
    protected $http;

    public static function setUpBeforeClass()
    {
        Server::boot(getenv('TEST_LUMEN_PORT'));
    }

    protected function setUp()
    {
        $this->http = new Fetch(['base_uri' => $this->baseUrl]);
    }

    protected function newHttp()
    {
        return new Fetch(['base_uri' => $this->baseUrl]);
    }
}
