<?php

namespace Storyblok;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

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
    function __construct($apiKey = null, $apiEndpoint = "mapi.storyblok.com", $apiVersion = "v1")
    {
    	parent::__construct($apiKey, $apiEndpoint, $apiVersion);
    }

    /**
     * @param ResponseInterface $responseObj
     * @return ManagementClient
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
     * @return ManagementClient
     * @throws ApiException|GuzzleException
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
        } catch (ClientException $e) {
            throw new ApiException(self::EXCEPTION_GENERIC_HTTP_ERROR . ' - ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $endpointUrl
     * @param array  $payload
     * @return ManagementClient
     * @throws ApiException|GuzzleException
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
        } catch (ClientException $e) {
            throw new ApiException(self::EXCEPTION_GENERIC_HTTP_ERROR . ' - ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $endpointUrl
     * @return ManagementClient
     * @throws ApiException|GuzzleException
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
        } catch (ClientException $e) {
            throw new ApiException(self::EXCEPTION_GENERIC_HTTP_ERROR . ' - ' . $e->getMessage(), $e->getCode());
        }
    }
}
