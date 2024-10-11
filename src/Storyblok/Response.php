<?php

namespace Storyblok;

use Psr\Http\Message\StreamInterface;

class Response
{
    public $httpResponseBody;

    public $httpResponseCode;

    public $httpResponseHeaders;

    public function getBody(): array
    {
        return $this->httpResponseBody;
    }

    /**
     * @param mixed $httpResponseBody
     */
    public function setBody($httpResponseBody): self
    {
        $this->httpResponseBody = $httpResponseBody;

        return $this;
    }

    public function setBodyFromStreamInterface(StreamInterface $body): self
    {
        $data = (string) $body;
        $jsonResponseData = (array) json_decode($data, true);
        // return response data as json if possible, raw if not
        $this->httpResponseBody = $data && [] === $jsonResponseData ? $data : $jsonResponseData;

        return $this;
    }

    public function getCode(): ?int
    {
        return $this->httpResponseCode;
    }

    /**
     * @param mixed $httpResponseCode
     */
    public function setCode($httpResponseCode): self
    {
        $this->httpResponseCode = $httpResponseCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->httpResponseHeaders;
    }

    /**
     * @param mixed $httpResponseHeaders
     */
    public function setHeaders($httpResponseHeaders): self
    {
        $this->httpResponseHeaders = $httpResponseHeaders;

        return $this;
    }
}
