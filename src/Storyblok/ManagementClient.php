<?php

namespace Storyblok;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;

/**
 * Storyblok Client.
 */
class ManagementClient extends BaseClient
{
    /**
     * @param string $apiEndpoint
     * @param mixed  $ssl
     */
    public function __construct(?string $apiKey = null, $apiEndpoint = 'mapi.storyblok.com', string $apiVersion = 'v1', $ssl = false)
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
     * @throws ApiException
     */
    public function post($endpointUrl, $payload): Response
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
        } catch (ClientException $clientException) {
            throw new ApiException(self::EXCEPTION_GENERIC_HTTP_ERROR . ' - ' . $clientException->getMessage(), $clientException->getCode());
        }
    }

    /**
     * @param string $endpointUrl
     * @param array  $payload
     *
     * @throws ApiException
     */
    public function put($endpointUrl, $payload): Response
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
        } catch (ClientException $clientException) {
            throw new ApiException(self::EXCEPTION_GENERIC_HTTP_ERROR . ' - ' . $clientException->getMessage(), $clientException->getCode());
        }
    }

    /**
     * @param string $endpointUrl
     *
     * @throws ApiException
     */
    public function delete($endpointUrl): Response
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
        } catch (ClientException $clientException) {
            throw new ApiException(self::EXCEPTION_GENERIC_HTTP_ERROR . ' - ' . $clientException->getMessage(), $clientException->getCode());
        }
    }
}
