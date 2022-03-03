<?php

namespace Storyblok;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

/**
* Storyblok Client
*/
class ManagementClient extends BaseClient
{
    /**
     * @param string $apiKey
     * @param string $apiEndpoint
     * @param string $apiVersion
     */
    function __construct($apiKey = null, $apiEndpoint = "mapi.storyblok.com", $apiVersion = "v1", $ssl = false)
    {
    	parent::__construct($apiKey, $apiEndpoint, $apiVersion, $ssl);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $responseObj
     *
     * @return \stdClass
     */
    public function responseHandler($responseObj)
    {
        $httpResponseCode = $responseObj->getStatusCode();
        $data = (string) $responseObj->getBody();
        $jsonResponseData = (array) json_decode($data, true);

        // return response data as json if possible, raw if not
        $this->responseBody = $data && empty($jsonResponseData) ? $data : $jsonResponseData;
        $this->responseCode = $httpResponseCode;
        $this->responseHeaders = $responseObj->getHeaders();
        return $this;
    }

    /**
     * @param string $endpointUrl
     * @param array  $payload
     *
     * @return \stdClass
     *
     * @throws ApiException
     */
    public function post($endpointUrl, $payload)
    {
        try {
            $requestOptions = [
            	RequestOptions::JSON => $payload,
            	RequestOptions::HEADERS => ['Authorization' => $this->getApiKey()]
            ];

            if ($this->getProxy()) {
                $requestOptions[RequestOptions::PROXY] = $this->getProxy();
            }

            $responseObj = $this->client->request('POST', $endpointUrl, $requestOptions);

            return $this->responseHandler($responseObj);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            throw new ApiException(self::EXCEPTION_GENERIC_HTTP_ERROR . ' - ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $endpointUrl
     * @param array  $payload
     *
     * @return \stdClass
     *
     * @throws ApiException
     */
    public function put($endpointUrl, $payload)
    {
        try {
            $requestOptions = [
            	RequestOptions::JSON => $payload,
            	RequestOptions::HEADERS => ['Authorization' => $this->getApiKey()]
            ];

            if ($this->getProxy()) {
                $requestOptions[RequestOptions::PROXY] = $this->getProxy();
            }

            $responseObj = $this->client->request('PUT', $endpointUrl, $requestOptions);

            return $this->responseHandler($responseObj);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            throw new ApiException(self::EXCEPTION_GENERIC_HTTP_ERROR . ' - ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $endpointUrl
     *
     * @return \stdClass
     *
     * @throws ApiException
     */
    public function delete($endpointUrl)
    {
        try {
            $requestOptions = [
            	RequestOptions::HEADERS => ['Authorization' => $this->getApiKey()]
            ];

            if ($this->getProxy()) {
                $requestOptions[RequestOptions::PROXY] = $this->getProxy();
            }

            $responseObj = $this->client->request('DELETE', $endpointUrl, $requestOptions);

            return $this->responseHandler($responseObj);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            throw new ApiException(self::EXCEPTION_GENERIC_HTTP_ERROR . ' - ' . $e->getMessage(), $e->getCode());
        }
    }
}
