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
     * @var \GuzzleHttp\Psr7\Response
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
    public function body(): string
    {
        return (string) $this->response->getBody();
    }

    /**
     * @return \GuzzleHttp\Psr7\Stream|\Psr\Http\Message\StreamInterface
     */
    public function stream()
    {
        return $this->response->getBody();
    }

    /**
     * @param bool $asArray
     * @return \stdClass|array
     */
    public function json(bool $asArray = false)
    {
        return \json_decode($this->response->getBody(), $asArray);
    }

    /**
     * @param string $header
     * @param bool   $asArray
     * @return string
     */
    public function header(string $header, bool $asArray = false)
    {
        if ($asArray) {
            return $this->response->getHeader($header);
        }

        return $this->response->getHeaderLine($header);
    }

    /**
     * @return array
     */
    public function headers()
    {
        return $this->response->getHeaders();
    }

    /**
     * @return string
     */
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
