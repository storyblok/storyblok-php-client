<?php

namespace Storyblok;

use GuzzleHttp\Client as Guzzle;
use Storyblok\Cache\Cache;

/**
* Storyblok Client
*/
class Storyblok
{
    const API_USER = "api";
    const SDK_VERSION = "1.0";
    const SDK_USER_AGENT = "storyblok-sdk-php";
    const EXCEPTION_GENERIC_HTTP_ERROR = "An HTTP Error has occurred! Check your network connection and try again.";

    /**
     * @var stdClass
     */
    private $responseBody;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $spacePath;

    /**
     * @var string
     */
    private $tempToken;
    
    /**
     * @var Guzzle
     */
    protected $client;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @param string $apiKey
     * @param string $apiEndpoint
     * @param string $apiVersion
     * @param bool   $ssl
     */
    function __construct($apiEndpoint = "api.storyblok.com", $apiKey = null, $apiVersion = "v1", $ssl = false)
    {
        $this->apiKey = $apiKey;
        $this->client = new Guzzle([
            'base_uri'=> $this->generateEndpoint($apiEndpoint, $apiVersion, $ssl),
            'defaults'=> [
                'auth' => array(self::API_USER, $this->apiKey),
                'exceptions' => false,
                'config' => ['curl' => [ CURLOPT_FORBID_REUSE => true ]],
                'headers' => [
                    'User-Agent' => self::SDK_USER_AGENT.'/'.self::SDK_VERSION,
                ],
            ],
        ]);

        if (isset($_GET['storybloktkn'])) {
            $this->tempToken = $_GET['storybloktkn'];
        } else {
            $this->cache = new Cache();
        }
    }

    /**
     * @param string $apiEndpoint
     * @param string $apiVersion
     * @param bool   $ssl
     *
     * @return string
     */
    private function generateEndpoint($apiEndpoint, $apiVersion, $ssl)
    {
        if (!$ssl) {
            return "http://".$apiEndpoint."/".$apiVersion."/";
        } else {
            return "https://".$apiEndpoint."/".$apiVersion."/";
        }
    }

    /**
     * @param string $endpointUrl
     * @param array  $queryString
     *
     * @return \stdClass
     *
     * @throws GenericHTTPError
     * @throws InvalidCredentials
     * @throws MissingEndpoint
     * @throws MissingRequiredParameters
     */
    public function get($endpointUrl, $queryString = array())
    {
        $response = $this->client->get($endpointUrl, ['query' => $queryString]);

        return $this->responseHandler($response);
    }

    /**
     * @param ResponseInterface $responseObj
     *
     * @return \stdClass
     *
     * @throws GenericHTTPError
     * @throws InvalidCredentials
     * @throws MissingEndpoint
     * @throws MissingRequiredParameters
     */
    public function responseHandler($responseObj)
    {
        $httpResponseCode = $responseObj->getStatusCode();
        if ($httpResponseCode === 200) {
            $data = (string) $responseObj->getBody();
            $jsonResponseData = json_decode($data, false);
            $result = new \stdClass();
            // return response data as json if possible, raw if not
            $result->http_response_body = $data && $jsonResponseData === null ? $data : $jsonResponseData;
        } else {
            throw new GenericHTTPError(self::EXCEPTION_GENERIC_HTTP_ERROR . $this->getResponseExceptionMessage($responseObj), $httpResponseCode, $responseObj->getBody());
        }
        $result->http_response_code = $httpResponseCode;

        return $result;
    }

    /**
     * @param \Guzzle\Http\Message\Response $responseObj
     *
     * @return string
     */
    protected function getResponseExceptionMessage(\GuzzleHttp\Message\Response $responseObj)
    {
        $body = (string) $responseObj->getBody();
        $response = json_decode($body);
        if (json_last_error() == JSON_ERROR_NONE && isset($response->message)) {
            return " ".$response->message;
        }
    }

    public function setCachePath($path)
    {
        if ($this->cache) {
            $this->cache->setCachePath($path);
        }
    }

    public function getStoryBySlug($slug)
    {
        
        if ($this->cache && $this->cache->contains($slug)) {
            $this->responseBody = $this->cache->fetch($slug);

            return $this->responseBody;
        } else {
            $options = array(
                'access_token' => $this->apiKey
            );

            $version = 'published';

            if ($this->tempToken) {
                $options['temp_token'] = $this->tempToken;
                $version = 'draft';
            }

            try {
                $response = $this->get($this->spacePath . 'stories/' . $version . '/' . $slug, $options);
                $this->responseBody = $response->http_response_body;

                if ($this->cache) {
                    $this->cache->save($slug, $this->responseBody);
                }

                return $this->responseBody;
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $request = $e->getRequest();
                $response = $e->getResponse();
                #echo $response->getReasonPhrase();
                return false;
            }
        }
    }

    public function setSpace($spaceId)
    {
        $this->spacePath = 'spaces/' . $spaceId . '/';
    }

    public function getStoryContent()
    {
        if (isset($this->responseBody->story->content)) {
            return (array)$this->responseBody->story->content;
        }
        return array();
    }
}