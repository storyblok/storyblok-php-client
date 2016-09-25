<?php

namespace Storyblok;

use GuzzleHttp\Client as Guzzle;
use Apix\Cache as ApixCache;

/**
* Storyblok Client
*/
class Client
{
    const API_USER = "api";
    const SDK_VERSION = "1.1";
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
    private $linksPath;

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
     * Enables editmode to receive draft versions
     * 
     * @param  boolean $enabled
     * @return \Client
     */
    public function editMode($enabled = true)
    {
        $this->editModeEnabled = $enabled;
        return $this;
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
        $response = json_decode($body);

        if (json_last_error() == JSON_ERROR_NONE && isset($response->message)) {
            return $response->message;
        }
    }

    /**
     * Set cache driver and optional the cache path
     * 
     * @param string $driver Driver
     * @param string $options Path for file cache
     * @return \Storyblok\Client
     */
    public function setCache($driver, $options = array())
    {
        $options['serializer'] = 'php';
        $options['prefix_key'] = 'storyblok:';
        $options['prefix_tag'] = 'storyblok:';

        switch ($driver) {
            case 'mysql':
                $dbh = $options['pdo'];
                $this->cache = new ApixCache\Pdo\Mysql($dbh, $options);

                break;

            case 'sqlite':
                $dbh = $options['pdo'];
                $this->cache = new ApixCache\Pdo\Sqlite($dbh, $options);

                break;

            case 'postgres':
                $dbh = $options['pdo'];
                $this->cache = new ApixCache\Pdo\Pgsql($dbh, $options);

                break;

            default:
                $options['directory'] = $options['path'];

                $this->cache = new ApixCache\Files($options);

                break;
        }

        return $this;
    }

    /**
     * Manually delete the cache of one item
     * 
     * @param  string $slug Slug
     * @return \Storyblok\Client
     */
    public function deleteCacheBySlug($slug)
    {
        $key = $this->spacePath . 'stories/published/' . $slug;

        if ($this->cache) {
            $this->cache->delete($key);

            // Always refresh cache of links
            $this->cache->delete($this->linksPath);
        }

        return $this;
    }

    /**
     * Automatically delete the cache of one item if client sends published parameter
     * 
     * @param  string $key Cache key
     * @return \Storyblok\Client
     */
    private function reCacheOnPublish($key)
    {
        if (isset($_GET['_storyblok_published']) && $this->cache && !$cachedItem = $this->cache->load($key)) {
            if (isset($cachedItem['story']) && $cachedItem['story']['id'] == $_GET['_storyblok_published']) {
                $this->cache->delete($key);
            }

            // Always refresh cache of links
            $this->cache->delete($this->linksPath);
        }

        return $this;
    }

    /**
     * Gets a story by the slug identifier
     * 
     * @param  string $slug Slug
     * @return \Storyblok\Client
     */
    public function getStoryBySlug($slug)
    {
        $version = 'published';

        if ($this->editModeEnabled) {
            $version = 'draft';
        }

        $key = $this->spacePath . 'stories/' . $slug;

        $this->reCacheOnPublish($key);

        if ($version == 'published' && $this->cache && $cachedItem = $this->cache->load($key)) {
            $this->responseBody = (array) $cachedItem;
        } else {
            $options = array(
                'token' => $this->apiKey,
                'version' => $version
            );

            $response = $this->get($key, $options);

            $this->responseBody = $response->httpResponseBody;

            if ($this->cache && $version == 'published') {
                $this->cache->save($this->responseBody, $key);
            }
        }

        return $this;
    }

    /**
     * Gets a list of links
     * 
     * @return \Storyblok\Client
     */
    public function getLinks()
    {
        $version = 'published';

        if ($this->editModeEnabled) {
            $version = 'draft';
        }

        if ($version == 'published' && $this->cache && $cachedItem = $this->cache->load($this->linksPath)) {
            $this->responseBody = (array) $cachedItem;
        } else {
            $options = array(
                'token' => $this->apiKey,
                'version' => $version
            );

            $response = $this->get($this->linksPath, $options);

            $this->responseBody = $response->httpResponseBody;

            if ($this->cache && $version == 'published') {
                $this->cache->save($this->responseBody, $this->linksPath);
            }
        }

        return $this;
    }

    /**
     * @deprecated
     */
    public function getStoryContent()
    {
        return $this->getBody();
    }

    /**
     * Gets the json response body
     * 
     * @return array
     */
    public function getBody()
    {
        if (isset($this->responseBody)) {
            return $this->responseBody;
        }

        return array();
    }

    /**
     * Transforms links into a tree
     * 
     * @param  string $slug Slug
     * @return \Client
     */
    public function getAsTree()
    {
        if (!isset($this->responseBody)) {
            return array();
        }

        $tree = [];

        foreach ($this->responseBody['links'] as $item) {
            if (!isset($tree[$item['parent_id']])) {
                $tree[$item['parent_id']] = array();
            }

            $tree[$item['parent_id']][] = $item;
        }

        return $this->_generateTree(0, $tree);
    }

    /**
     * Recursive function to generate tree
     * 
     * @param  integer $parent
     * @param  array  $items
     * @return array
     */
    private function _generateTree($parent = 0, $items)
    {
        $tree = array();

        if (isset($items[$parent])) {
            $result = $items[$parent];

            foreach ($result as $item) {
                if (!isset($tree[$item['id']])) {
                    $tree[$item['id']] = array();
                }

                $tree[$item['id']]['item']  = $item;
                $tree[$item['id']]['children']  = $this->_generateTree($item['id'], $items);
            }
        }

        return $tree;
    }

    /**
     * Sets the space id
     * 
     * @param string $spaceId
     */
    public function setSpace($spaceId)
    {
        $this->spacePath = 'cdn/spaces/' . $spaceId . '/';
        $this->linksPath = $this->spacePath . 'links/';

        return $this;
    }
}