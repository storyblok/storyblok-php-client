<?php

namespace Storyblok;

use GuzzleHttp\RequestOptions;

/**
 * Storyblok Client.
 */
class ManagementClient extends BaseClient
{
    /**
     * @param string $apiKey
     * @param string $apiEndpoint
     * @param string $apiVersion
     * @param mixed  $ssl
     */
    function __construct($apiKey = null, $apiEndpoint = 'mapi.storyblok.com', $apiVersion = 'v1', $ssl = false)
    {
        parent::__construct($apiKey, $apiEndpoint, $apiVersion, $ssl);
    }

    public function responseHandler($responseObj, $queryString = null): Response
    {
        return parent::responseHandler($responseObj, $queryString);
    }

    /**
     * @param string $endpointUrl
     * @param array  $payload
     *
     * @return Response
     *
     * @throws ApiException
     */
    public function post($endpointUrl, $payload)
    {
        try {
            $requestOptions = [
                RequestOptions::JSON => $payload,
                RequestOptions::HEADERS => ['Authorization' => $this->getApiKey()],
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
     * @return Response
     *
     * @throws ApiException
     */
    public function put($endpointUrl, $payload)
    {
        try {
            $requestOptions = [
                RequestOptions::JSON => $payload,
                RequestOptions::HEADERS => ['Authorization' => $this->getApiKey()],
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
     * @return Response
     *
     * @throws ApiException
     */
    public function delete($endpointUrl)
    {
        try {
            $requestOptions = [
                RequestOptions::HEADERS => ['Authorization' => $this->getApiKey()],
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
