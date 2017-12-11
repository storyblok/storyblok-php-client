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
    const SDK_VERSION = "1.2";
    const CACHE_VERSION_KEY = "storyblok:cache_version";
    const SDK_USER_AGENT = "storyblok-sdk-php";
    const EXCEPTION_GENERIC_HTTP_ERROR = "An HTTP Error has occurred!";

    /**
     * @var stdClass
     */
    private $responseBody;

    /**
     * @var stdClass
     */
    private $responseHeaders;

    /**
     * @var string
     */
    public $cacheVersion;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $linksPath = 'links/';

    /**
     * @var boolean
     */
    private $editModeEnabled;

    /**
     * @var boolean
     */
    private $cacheNotFound;

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
     * Enables caching for 404 responses
     *
     * @param  boolean $enabled
     * @return \Client
     */
    public function cacheNotFound($enabled = true)
    {
        $this->cacheNotFound = $enabled;
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
            return "http://".$apiEndpoint."/".$apiVersion."/cdn/";
        } else {
            return "https://".$apiEndpoint."/".$apiVersion."/cdn/";
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
            throw new \Exception(self::EXCEPTION_GENERIC_HTTP_ERROR . ' - ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param ResponseInterface $responseObj
     *
     * @return \stdClass
     */
    public function responseHandler($responseObj)
    {
        $httpResponseCode = $responseObj->getStatusCode();
        $data = (string) $responseObj->getBody();
        $jsonResponseData = (array) json_decode($data, true);
        $result = new \stdClass();

        // return response data as json if possible, raw if not
        $result->httpResponseBody = $data && empty($jsonResponseData) ? $data : $jsonResponseData;
        $result->httpResponseCode = $httpResponseCode;
        $result->httpResponseHeaders = $responseObj->getHeaders();
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

        $this->cacheVersion = $this->cache->load(self::CACHE_VERSION_KEY);

        if (!$this->cacheVersion) {
            $this->setCacheVersion();
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
        $key = $this->_getCacheKey('stories/' . $slug);

        if ($this->cache) {
            $this->cache->delete($key);

            // Always refresh cache of links
            $this->cache->delete($this->linksPath);
            $this->setCacheVersion();
        }

        return $this;
    }

    /**
     * Flush all cache
     *
     * @return \Storyblok\Client
     */
    public function flushCache()
    {
        if ($this->cache) {
            $this->cache->flush();
            $this->setCacheVersion();
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
        if (isset($_GET['_storyblok_published']) && $this->cache) {
            $this->cache->delete($key);

            // Always refresh cache of links
            $this->cache->delete($this->linksPath);
            $this->setCacheVersion();
        }

        return $this;
    }

    /**
     * Sets cache version to get a fresh version from cdn after clearing the cache
     *
     * @return \Storyblok\Client
     */
    public function setCacheVersion()
    {
        if ($this->cache) {
            $timestamp = time();
            $this->cache->save($timestamp, self::CACHE_VERSION_KEY);
            $this->cacheVersion = $timestamp;
        }

        return $this;
    }

    /**
     * Gets cache version from cache or as timestamp
     * 
     * @return Integer
     */
    function getCacheVersion()
    {
        if (empty($this->cacheVersion)) {
            return time();
        } else {
            return $this->cacheVersion;
        }
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

        $key = 'stories/' . $slug;
        $cachekey = $this->_getCacheKey($key);

        $this->reCacheOnPublish($key);

        if ($version == 'published' && $this->cache && $cachedItem = $this->cache->load($cachekey)) {
            if ($this->cacheNotFound && $cachedItem->httpResponseCode == 404) {
                throw new \Exception(self::EXCEPTION_GENERIC_HTTP_ERROR, 404);
            }

            $this->_assignState($cachedItem);
        } else {
            $options = array(
                'token' => $this->apiKey,
                'version' => $version,
                'cache_version' => $this->getCacheVersion()
            );

            try {
                $response = $this->get($key, $options);
                $this->_save($response, $cachekey, $version);
            } catch (\Exception $e) {
                if ($this->cacheNotFound && $e->getCode() === 404) {
                    $result = new \stdClass();
                    $result->httpResponseBody = [];
                    $result->httpResponseCode = 404;
                    $result->httpResponseHeaders = [];

                    $this->cache->save($result, $cachekey);
                }

                throw new \Exception(self::EXCEPTION_GENERIC_HTTP_ERROR . ' - ' . $e->getMessage(), $e->getCode());
            }
        }

        return $this;
    }

    /**
     * Gets a list of stories
     *
     * array(
     *    'starts_with' => $slug,
     *    'with_tag' => $tag,
     *    'sort_by' => $sort_by,
     *    'per_page' => 25,
     *    'page' => 0
     * )
     *
     *
     * @param  array $options Options
     * @return \Storyblok\Client
     */
    public function getStories($options = array())
    {
        $version = 'published';
        $endpointUrl = 'stories/';

        if ($this->editModeEnabled) {
            $version = 'draft';
        }

        $key = 'stories/' . serialize($this->_prepareOptionsForKey($options));
        $cachekey = $this->_getCacheKey($key);

        $this->reCacheOnPublish($key);

        if ($version == 'published' && $this->cache && $cachedItem = $this->cache->load($cachekey)) {
            $this->_assignState($cachedItem);
        } else {
            $options = array_merge($options, array(
                'token' => $this->apiKey,
                'version' => $version,
                'cache_version' => $this->getCacheVersion()
            ));

            $response = $this->get($endpointUrl, $options);

            $this->_save($response, $cachekey, $version);
        }

        return $this;
    }


    /**
     * Gets a list of tags
     *
     * array(
     *    'starts_with' => $slug
     * )
     *
     *
     * @param  array $options Options
     * @return \Storyblok\Client
     */
    public function getTags($options = array())
    {
        $version = 'published';
        $endpointUrl = 'tags/';

        if ($this->editModeEnabled) {
            $version = 'draft';
        }

        $key = 'tags/' . serialize($options);
        $cachekey = $this->_getCacheKey($key);

        $this->reCacheOnPublish($key);

        if ($version == 'published' && $this->cache && $cachedItem = $this->cache->load($cachekey)) {
            $this->_assignState($cachedItem);
        } else {
            $options = array_merge($options, array(
                'token' => $this->apiKey,
                'version' => $version,
                'cache_version' => $this->getCacheVersion()
            ));

            $response = $this->get($endpointUrl, $options);

            $this->_save($response, $cachekey, $version);
        }

        return $this;
    }

    /**
     * Gets a list of datasource entries
     *
     * @param  string $slug Slug
     * @param  array $options Options
     * @return \Storyblok\Client
     */
    public function getDatasourceEntries($slug, $options = array())
    {
        $version = 'published';
        $endpointUrl = 'datasource_entries/';

        if ($this->editModeEnabled) {
            $version = 'draft';
        }

        $key = 'datasource_entries/' . $slug . '/' . serialize($options);
        $cachekey = $this->_getCacheKey($key);

        $this->reCacheOnPublish($key);

        if ($version == 'published' && $this->cache && $cachedItem = $this->cache->load($cachekey)) {
            $this->_assignState($cachedItem);
        } else {
            $options = array_merge($options, array(
                'token' => $this->apiKey,
                'version' => $version,
                'cache_version' => $this->getCacheVersion(),
                'datasource' => $slug
            ));

            $response = $this->get($endpointUrl, $options);

            $this->_save($response, $cachekey, $version);
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

        $key = $this->linksPath;
        $cachekey = $this->_getCacheKey($key);

        if ($this->editModeEnabled) {
            $version = 'draft';
        }

        if ($version == 'published' && $this->cache && $cachedItem = $this->cache->load($cachekey)) {
            $this->_assignState($cachedItem);
        } else {
            $options = array(
                'token' => $this->apiKey,
                'version' => $version,
                'cache_version' => $this->getCacheVersion()
            );

            $response = $this->get($key, $options);

            $this->_save($response, $cachekey, $version);
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
     * Gets the response headers
     *
     * @return array
     */
    public function getHeaders()
    {
        if (isset($this->responseHeaders)) {
            return $this->responseHeaders;
        }

        return array();
    }

    /**
     * Transforms datasources into a ['name']['value'] array.
     *
     * @return array
     */
    public function getAsNameValueArray()
    {
        if (!isset($this->responseBody)) {
            return array();
        }

        $array = [];

        foreach ($this->responseBody['datasource_entries'] as $entry) {
            if (!isset($array[$entry['name']])) {
                $array[$entry['name']] = $entry['value'];
            }
        }

        return $array;
    }

    /**
     * Transforms tags into a string array.
     *
     * @return array
     */
    public function getTagsAsStringArray()
    {
        if (!isset($this->responseBody)) {
            return array();
        }

        $array = [];

        foreach ($this->responseBody['tags'] as $entry) {
            array_push($array, $entry['name']);
        }

        return $array;
    }

    /**
     * Transforms links into a tree
     *
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
     * Save's the current response in the cache if version is published
     *
     * @param  array $response
     * @param  string $key
     * @param  string $version
     */
    private function _save($response, $key, $version)
    {
        $this->_assignState($response);

        if ($this->cache &&
            $version == 'published' &&
            $response->httpResponseHeaders &&
            $response->httpResponseCode == 200) {

            $this->cache->save($response, $key);
        }
    }

    /**
     * Assigns the httpResponseBody and httpResponseHeader to '$this';
     *
     * @param  array $response
     * @param  string $key
     * @param  string $version
     */
    private function _assignState($response) {
        $this->responseBody = $response->httpResponseBody;
        $this->responseHeaders = $response->httpResponseHeaders;
    }

    /**
     * prepares to Options for the cache key. Fixes some issues for too long filenames if filecache is used.
     *
     * @param  array $response
     * @param  string $key
     * @param  string $version
     */
    private function _prepareOptionsForKey($options) {
        $prepared = array();
        $keyOrder = array();
        foreach($options as $key => $value) {
           array_push($prepared, $value);
           array_push($keyOrder, substr($key, 0, 1));
       }
       array_push($prepared, join('', $keyOrder));
       return $prepared;
    }

    private function _getCacheKey($key = '')
    {
        return hash('sha256', $key);
    }
}
