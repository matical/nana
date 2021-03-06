<?php

namespace ksmz\nana;

use Psr\Http\Message\ResponseInterface;

/** @mixin \GuzzleHttp\Psr7\Response */
class Consume
{
    use InteractsWithStatuses, IsMacroable {
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
        return $asArray ? $this->response->getHeader($header) : $this->response->getHeaderLine($header);
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
