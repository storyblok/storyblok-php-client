<?php

namespace Storyblok;

use Psr\Http\Message\StreamInterface;

class Response
{
    public $httpResponseBody;

    public $httpResponseCode;

    public $httpResponseHeaders;

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->httpResponseBody;
    }

    /**
     * @param mixed $httpResponseBody
     *
     * @return Response
     */
    public function setBody($httpResponseBody)
    {
        $this->httpResponseBody = $httpResponseBody;

        return $this;
    }

    public function setBodyFromStreamInterface(StreamInterface $body): self
    {
        $data = (string) $body;
        $jsonResponseData = (array) json_decode($data, true);
        // return response data as json if possible, raw if not
        $this->httpResponseBody = $data && empty($jsonResponseData) ? $data : $jsonResponseData;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getCode()
    {
        return $this->httpResponseCode;
    }

    /**
     * @param mixed $httpResponseCode
     *
     * @return Response
     */
    public function setCode($httpResponseCode)
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
     *
     * @return Response
     */
    public function setHeaders($httpResponseHeaders)
    {
        $this->httpResponseHeaders = $httpResponseHeaders;

        return $this;
    }
}
