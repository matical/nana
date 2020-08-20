<?php

namespace ksmz\nana;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions as Options;
use Psr\Http\Message\ResponseInterface;

class Fetch
{
    /**
     * Either one of 'body', 'json', 'form_params' and 'multipart'.
     * Supported formats consts defined in 'GuzzleHttp\RequestOptions'.
     *
     * @var string
     */
    protected $bodyFormat = 'json';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $defaultOptions = [
        'http_errors' => false,
        'headers'     => [
            'User-Agent' => 'nana/0.1',
            'Accept'     => 'application/json',
        ],
    ];

    /**
     * @var array
     */
    protected $availableBodyFormats = [
        Options::BODY,
        Options::JSON,
        Options::FORM_PARAMS,
        Options::MULTIPART,
    ];

    /**
     * Fetch constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = empty($options) ? $this->defaultOptions : $options;
    }

    /**
     * @param mixed ...$args
     * @return static
     */
    public static function newInstance(...$args)
    {
        return new static(...$args);
    }

    /**
     * @param array $options
     * @return self
     */
    public function withOptions(array $options)
    {
        $this->options = $this->mergeOptions($options);

        return $this;
    }

    /**
     * @param array $headers
     * @return self
     */
    public function withHeaders(array $headers)
    {
        $this->options = $this->mergeOptions(['headers' => $headers]);

        return $this;
    }

    /**
     * @param array $queries
     * @return self
     */
    public function withQueries(array $queries)
    {
        $this->options = $this->mergeOptions(['query' => $queries]);

        return $this;
    }

    /**
     * @return self
     */
    public function asJson()
    {
        return $this->bodyFormat(Options::JSON);
    }

    /**
     * @return self
     */
    public function asFormParams()
    {
        return $this->bodyFormat(Options::FORM_PARAMS);
    }

    /**
     * @return self
     */
    public function asMultipart()
    {
        return $this->bodyFormat(Options::MULTIPART);
    }

    /**
     * Configure http_errors.
     *
     * @param bool $enabled
     * @return self
     */
    public function httpErrors(bool $enabled)
    {
        $this->overrideOptions('http_errors', $enabled);

        return $this;
    }

    /**
     * Configure the 'User-Agent' header.
     *
     * @param string $userAgent
     * @return self
     */
    public function userAgent(string $userAgent)
    {
        $this->overrideHeader('User-Agent', $userAgent);

        return $this;
    }

    /**
     * Configure the 'Accept' header.
     *
     * @param string $accept
     * @return self
     */
    public function accepts(string $accept)
    {
        $this->overrideHeader('Accept', $accept);

        return $this;
    }

    /**
     * Set a file path for Guzzle's sink.
     *
     * @param string $path
     * @return self
     */
    public function saveTo(string $path)
    {
        $this->options = $this->mergeOptions(['sink' => $path]);

        return $this;
    }

    /**
     * @return self
     */
    public function once()
    {
        return clone $this;
    }

    /**
     * Executes a GET.
     *
     * @param string $url
     * @param array  $queryParams
     * @return \ksmz\nana\Consume
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $url, array $queryParams = [])
    {
        return $this->send('GET', $url, [
            'query' => $queryParams,
        ]);
    }

    /**
     * Executes a POST.
     *
     * @param string $url
     * @param array  $params
     * @return \ksmz\nana\Consume
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(string $url, array $params = [])
    {
        return $this->send('POST', $url, [
            $this->bodyFormat => $params,
        ]);
    }

    /**
     * Executes a PATCH.
     *
     * @param string $url
     * @param array  $params
     * @return \ksmz\nana\Consume
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function patch(string $url, array $params = [])
    {
        return $this->send('PATCH', $url, [
            $this->bodyFormat => $params,
        ]);
    }

    /**
     * Executes a PUT.
     *
     * @param string $url
     * @param array  $params
     * @return \ksmz\nana\Consume
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put(string $url, array $params = [])
    {
        return $this->send('PUT', $url, [
            $this->bodyFormat => $params,
        ]);
    }

    /**
     * Executes a DELETE.
     *
     * @param string $url
     * @param array  $params
     * @return \ksmz\nana\Consume
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(string $url, array $params = [])
    {
        return $this->send('DELETE', $url, [
            $this->bodyFormat => $params,
        ]);
    }

    /**
     * Build the client and send the request.
     *
     * @param string $method
     * @param string $url
     * @param array  $options
     * @return \ksmz\nana\Consume
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(string $method, string $url, array $options)
    {
        try {
            // Guzzle overwrites all query string values in the URI if queries are specified
            // in the 'query' option, so they need to be extracted first.
            $optionsPayload = $this->mergeOptions(
                ['query' => $this->parseQueryParams($url)],
                $options
            );

            $response = $this->buildClient()->request($method, $url, $optionsPayload);

            return $this->buildResponse($response);
        } catch (GuzzleException $exception) {
            // No "custom" exceptions, just rethrow whatever guzzle spits out.
            throw $exception;
        }
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient()
    {
        return $this->buildClient($this->options);
    }

    /**
     * Build a new response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \ksmz\nana\Consume
     */
    protected function buildResponse(ResponseInterface $response)
    {
        return new Consume($response);
    }

    /**
     * Set the request to the specified format.
     *
     * @param $format
     * @return self
     */
    protected function bodyFormat($format)
    {
        $this->bodyFormat = $format;

        return $this;
    }

    /**
     * @param array $options
     * @return \GuzzleHttp\Client
     */
    protected function buildClient(array $options = [])
    {
        return new Client($options);
    }

    /**
     * @param mixed ...$options
     * @return array
     */
    protected function mergeOptions(...$options)
    {
        return \array_merge_recursive($this->options, ...$options);
    }

    /**
     * Override and replace an option.
     *
     * @param string       $option
     * @param string|array $value
     * @return self
     */
    protected function overrideOptions(string $option, $value)
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * Override and replace a header.
     *
     * @param string       $header
     * @param string|array $value
     * @return self
     */
    protected function overrideHeader(string $header, $value)
    {
        // Vs mergeOptions/pokeHeader, this ensures only one of the specific headers is set.
        $this->options['headers'][$header] = $value;

        return $this;
    }

    /**
     * Extracts query params into an array.
     *
     * @param string $url
     * @return array
     *
     * @see http://docs.guzzlephp.org/en/stable/request-options.html#query
     */
    protected function parseQueryParams($url)
    {
        $queries = [];

        $querySegment = \parse_url($url, PHP_URL_QUERY);
        \parse_str($querySegment, $queries);

        return $queries;
    }
}
