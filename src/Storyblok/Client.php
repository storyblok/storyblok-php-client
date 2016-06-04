<?php

namespace Storyblok;

use GuzzleHttp\Client as Guzzle;

/**
* Storyblok Client
*/
class Client
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
    private $editModeEnabled;
    
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
    function __construct($apiKey = null, $apiEndpoint = "api.storyblok.com", $apiVersion = "v1", $ssl = false)
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

        if (isset($_GET['_storyblok'])) {
            $this->editModeEnabled = $_GET['_storyblok'];
        } else {
            $this->editModeEnabled = false;
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
     * @throws Exception
     */
    public function get($endpointUrl, $queryString = array())
    {
        try {
            $responseObj = $this->client->get($endpointUrl, ['query' => $queryString]);
            return $this->responseHandler($responseObj);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            throw new \Exception(self::EXCEPTION_GENERIC_HTTP_ERROR);
        }
    }

    /**
     * @param ResponseInterface $responseObj
     *
     * @return \stdClass
     *
     * @throws Exception
     */
    public function responseHandler($responseObj)
    {
        $httpResponseCode = $responseObj->getStatusCode();
        if ($httpResponseCode === 200) {
            $data = (string) $responseObj->getBody();
            $jsonResponseData = (array) json_decode($data, true);
            $result = new \stdClass();
            // return response data as json if possible, raw if not
            $result->httpResponseBody = $data && empty($jsonResponseData) ? $data : $jsonResponseData;
        } else {
            throw new \Exception(self::EXCEPTION_GENERIC_HTTP_ERROR . $this->getResponseExceptionMessage($responseObj), $httpResponseCode, $responseObj->getBody());
        }
        $result->httpResponseCode = $httpResponseCode;

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
        $response = (array) json_decode($body, true);

        if (json_last_error() == JSON_ERROR_NONE && isset($response->message)) {
            return " ".$response->message;
        }
    }

    /**
     * Set cache driver and optional the cache path
     * 
     * @param string $driver Driver
     * @param string $path   Path for file cache
     * @return \Storyblok
     */
    public function setCache($driver, $path = 'cache')
    {
        switch ($driver) {
            // TODO: add other drivers
            
            default:
                $this->cache = new \Doctrine\Common\Cache\FilesystemCache($path);
                break;
        }

        return $this;
    }

    /**
     * Automatically delete the cache of one item if client sends published parameter
     * 
     * @param  string $slug Cache key
     * @return \Storyblok
     */
    private function reCacheOnPublishBySlug($slug)
    {
        if (isset($_GET['_storyblok_published']) && $this->cache) {
            $this->cache->delete($slug);
        }

        return $this;
    }

    /**
     * Gets a story by the slug identifier
     * 
     * @param  string $slug Slug
     * @return \Storyblok
     */
    public function getStoryBySlug($slug)
    {
        $version = 'published';

        if ($this->editModeEnabled) {
            $version = 'draft';
        }

        $key = $this->spacePath . 'stories/' . $version . '/' . $slug;

        $this->reCacheOnPublishBySlug($key);

        if ($version == 'published' && $this->cache && $this->cache->contains($key)) {
            $this->responseBody = $this->cache->fetch($key);
        } else {
            $options = array(
                'token' => $this->apiKey
            );

            $response = $this->get($key, $options);

            $this->responseBody = $response->httpResponseBody;

            if ($this->cache && $version == 'published') {
                $this->cache->save($key, $this->responseBody);
            }
        }
        return $this;
    }

    /**
     * Sets the space id
     * 
     * @param string $spaceId
     */
    public function setSpace($spaceId)
    {
        $this->spacePath = 'spaces/' . $spaceId . '/';

        return $this;
    }

    /**
     * Gets the json response body as an array
     * 
     * @return array
     */
    public function getStoryContent()
    {
        if (isset($this->responseBody)) {
            return $this->responseBody;
        }

        return array();
    }
}