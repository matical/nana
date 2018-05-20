<?php

namespace ksmz\nana;

/** @property \GuzzleHttp\Psr7\Response $response */
trait InteractsWithStatuses
{
    /**
     * @return int
     */
    public function status()
    {
        return $this->response->getStatusCode();
    }

    /**
     * @return bool
     */
    public function isOk()
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    /**
     * @return bool
     */
    public function isRedirection()
    {
        return $this->status() >= 300 && $this->status() < 400;
    }

    /**
     * @return bool
     */
    public function isClientError()
    {
        return $this->status() >= 400 && $this->status() < 500;
    }

    /**
     * @return bool
     */
    public function isServerError()
    {
        return $this->status() >= 500;
    }
}
