<?php

namespace ksmz\nana;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions as Options;

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
    ];

    /**
     * @var array
     */
    protected $defaultHeaders = [
        'User-Agent' => 'nana/0.1',
        'Accept'     => 'application/json',
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
        $this->options['headers'] = $this->defaultHeaders;
    }

    /**
     * @param mixed ...$args
     * @return static
     */
    public static function new(...$args)
    {
        return new static(...$args);
    }

    /**
     * @param $options
     * @return self
     */
    public function withOptions($options)
    {
        $this->options = $this->mergeOptions($options);

        return $this;
    }

    /**
     * @param $headers
     * @return self
     */
    public function withHeaders($headers)
    {
        $this->options = $this->mergeOptions(['headers' => $headers]);

        return $this;
    }

    /**
     * @return \ksmz\nana\Fetch
     */
    public function asJson()
    {
        return $this->bodyFormat(Options::JSON);
    }

    /**
     * @return \ksmz\nana\Fetch
     */
    public function asFormParams()
    {
        return $this->bodyFormat(Options::FORM_PARAMS);
    }

    /**
     * @return \ksmz\nana\Fetch
     */
    public function asMultipart()
    {
        return $this->bodyFormat(Options::MULTIPART);
    }

    /**
     * @param string $userAgent
     * @return self
     */
    public function asUserAgent(string $userAgent)
    {
        $this->overrideHeader('User-Agent', $userAgent);

        return $this;
    }

    /**
     * @param $accept
     * @return self
     */
    public function accepts($accept)
    {
        $this->overrideHeader('Accept', $accept);

        return $this;
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

            $client = $this->buildClient()->request($method, $url, $optionsPayload);

            return new Consume($client);
        } catch (GuzzleException $exception) {
            // No "custom" exceptions, just rethrow whatever guzzle spits out.
            throw $exception;
        }
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
     * @return \GuzzleHttp\Client
     */
    protected function buildClient()
    {
        return new Client();
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
     * Override and replace a header.
     *
     * @param $header
     * @param $value
     * @return self
     */
    protected function overrideHeader($header, $value)
    {
        // Vs mergeOptions/pokeHeader, this ensures only one of the specific headers is set.
        $this->options['headers'][$header] = $value;

        return $this;
    }

    /**
     * Extracts query params into an array.
     *
     * @param $url
     * @return array
     *
     * @see http://docs.guzzlephp.org/en/stable/request-options.html#query
     */
    protected function parseQueryParams($url)
    {
        return $this->tap([], function (&$queryStrings) use ($url) {
            \parse_str(\parse_url($url, PHP_URL_QUERY), $queryStrings);
        });
    }

    /**
     * Call the given Closure with the given value then return the value.
     *
     * @param mixed         $value
     * @param callable|null $callback
     * @return mixed
     */
    protected function tap($value, $callback = null)
    {
        $callback($value);

        return $value;
    }
}
