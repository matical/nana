<?php

namespace ksmz\nana;

use Spatie\Macroable\Macroable;
use Psr\Http\Message\ResponseInterface;

/** @mixin \GuzzleHttp\Psr7\Response */
class Consume
{
    use InteractsWithStatuses, Macroable {
        __call as macroCall;
    }

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    /**
     * Response constructor.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function body()
    {
        return (string) $this->response->getBody();
    }

    /**
     * @param bool $asArray
     * @return mixed
     */
    public function json($asArray = false)
    {
        return json_decode($this->response->getBody(), $asArray);
    }

    /**
     * @param $header
     * @return string
     */
    public function header($header)
    {
        return $this->response->getHeaderLine($header);
    }

    public function __toString()
    {
        return $this->body();
    }

    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->response->{$method}(...$parameters);
    }
}
