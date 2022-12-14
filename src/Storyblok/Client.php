<?php

namespace Storyblok;

// use Apix\Cache as ApixCache;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\PdoAdapter;
use Symfony\Component\Cache as SymfonyCache;

/**
 * Storyblok Client.
 */
class Client extends BaseClient
{
    const CACHE_VERSION_KEY = 'storyblok_cv';
    const EXCEPTION_GENERIC_HTTP_ERROR = 'An HTTP Error has occurred!';

    /**
     * @var string
     */
    public $cacheVersion;

    /**
     * @var SymfonyCache
     */
    protected $cache;

    /**
     * @var string
     */
    protected $language = 'default';

    /**
     * @var string
     */
    protected $fallbackLanguage = 'default';

    /**
     * @var string
     */
    private $linksPath = 'links/';

    /**
     * @var bool
     */
    private $editModeEnabled;

    /**
     * @var string
     */
    private $release;

    /**
     * @var string
     */
    private $resolveRelations;

    /**
     * @var string
     */
    private $resolveLinks;

    /**
     * @var bool
     */
    private $cacheNotFound;
    /**
     * @var null|mixed
     */
    private $cv;

    /**
     * @param string     $apiKey
     * @param string     $apiEndpoint
     * @param string     $apiVersion
     * @param bool       $ssl
     * @param null|mixed $apiRegion
     */
    function __construct($apiKey = null, $apiEndpoint = null, $apiVersion = 'v2', $ssl = false, $apiRegion = null)
    {
        parent::__construct($apiKey, $apiEndpoint, $apiVersion, $ssl, $apiRegion);

        if (isset($_GET['_storyblok'])) {
            $this->editModeEnabled = $_GET['_storyblok'];
            $this->release = $_GET['_storyblok_release'] ?? null;
        } else {
            $this->editModeEnabled = false;
        }
    }

    /**
     * Enables editmode to receive draft versions.
     *
     * @param bool $enabled
     *
     * @return Client
     */
    public function editMode($enabled = true)
    {
        $this->editModeEnabled = $enabled;

        return $this;
    }

    /**
     * Set the language the story should be retrieved in.
     *
     * @param string $language
     *
     * @return Client
     */
    public function language($language = 'default')
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get the language the story should be retrieved in.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set the fallback language the story should be retrieved in.
     *
     * @param string $fallbackLanguage
     *
     * @return Client
     */
    public function fallbackLanguage($fallbackLanguage = 'default')
    {
        $this->fallbackLanguage = $fallbackLanguage;

        return $this;
    }

    /**
     * Get the fallback language the story should be retrieved in.
     *
     * @return string
     */
    public function getFallbackLanguage()
    {
        return $this->fallbackLanguage;
    }

    /**
     * Enables caching for 404 responses.
     *
     * @param bool $enabled
     *
     * @return Client
     */
    public function cacheNotFound($enabled = true)
    {
        $this->cacheNotFound = $enabled;

        return $this;
    }

    /**
     * Returns the requested version of the content.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->editModeEnabled ? 'draft' : 'published';
    }

    /**
     * Returns the commond API parameters.
     *
     * @return array
     */
    public function getApiParameters()
    {
        return [
            'token' => $this->getApiKey(),
            'version' => $this->getVersion(),
            'cv' => $this->getCacheVersion(),
        ];
    }

    /**
     * Set cache driver and optional the cache path.
     *
     * @param string $driver  Driver
     * @param array  $options Path for file cache
     *
     * @return \Storyblok\Client
     */
    public function setCache($driver, $options = [])
    {
        $options['serializer'] = 'php';
        $options['prefix_key'] = 'storyblokcache';
        $options['prefix_tag'] = 'storyblokcachetag';

        switch ($driver) {
            case 'mysql':
                $dbh = $options['pdo'];
                $this->cache = new PdoAdapter(
                    $dbh,
                    $options['prefix_key'],
                    0,
                    $options
                );

                break;

            case 'sqlite':
                $dbh = $options['pdo'];
                $this->cache = new PdoAdapter(
                    $dbh,
                    $options['prefix_key'],
                    0,
                    $options
                );

                break;

            case 'postgres':
                $dbh = $options['pdo'];
                $this->cache = new PdoAdapter(
                    $dbh,
                    $options['prefix_key'],
                    0,
                    $options
                );

                break;

            default:
                $options['directory'] = $options['path'];

                $this->cache = new FilesystemAdapter(
                    $options['prefix_key'],
                    0,
                    $options['directory']
                );

                break;
        }

        $this->cv = $this->cacheGet(self::CACHE_VERSION_KEY);

        if (!$this->cv) {
            $this->cacheSave('', self::CACHE_VERSION_KEY);
            // $this->setCacheVersion();
        }

        return $this;
    }

    /**
     * Manually delete the cache of one item.
     *
     * @param string $slug Slug
     *
     * @return \Storyblok\Client
     */
    public function deleteCacheBySlug($slug)
    {
        $key = $this->_getCacheKey('stories/' . $slug);
        $linksCacheKey = $this->_getCacheKey($this->linksPath);
        if ($this->cache) {
            $this->cache->delete($key);

            // Always refresh cache of links
            $this->cache->delete($linksCacheKey);
            $this->setCacheVersion();
        }

        return $this;
    }

    /**
     * Flush all cache.
     *
     * @return \Storyblok\Client
     */
    public function flushCache()
    {
        if ($this->cache) {
            $this->cache->clear();
            $this->setCacheVersion();
        }

        return $this;
    }

    /**
     * Sets cache version to get a fresh version from cdn after clearing the cache.
     *
     * @return \Storyblok\Client
     */
    public function setCacheVersion()
    {
        if ($this->cache) {
            $res = $this->getStories(['per_page' => 1, 'version' => 'published']);
            $this->cv = $res->responseBody['cv'];
            $this->cacheSave($this->cv, self::CACHE_VERSION_KEY);
        }

        return $this;
    }

    /**
     * Gets cache version from cache or as timestamp.
     *
     * @return int
     */
    function getCacheVersion()
    {
        if (empty($this->cv)) {
            return '';
        }

        return $this->cv;
    }

    /**
     * Pull story from a release for preview.
     *
     * @param string $release
     *
     * @return Client
     */
    public function setRelease($release)
    {
        $this->release = $release;

        return $this;
    }

    /**
     * Gets a story by the slug identifier.
     *
     * @param string $slug Slug
     *
     * @return Client
     *
     * @throws ApiException
     */
    public function getStoryBySlug($slug)
    {
        return $this->getStory($slug);
    }

    /**
     * Gets a story by it’s UUID.
     *
     * @param string $uuid UUID
     *
     * @return Client
     *
     * @throws ApiException
     */
    public function getStoryByUuid($uuid)
    {
        return $this->getStory($uuid, true);
    }

    /**
     * Gets a list of stories.
     *
     * array(
     *    'starts_with' => $slug,
     *    'with_tag' => $tag,
     *    'sort_by' => $sort_by,
     *    'per_page' => 25,
     *    'page' => 0
     * )
     *
     * @param array $options Options
     *
     * @return \Storyblok\Client
     */
    public function getStories($options = [])
    {
        $endpointUrl = 'stories/';

        $key = 'stories/' . serialize($this->_prepareOptionsForKey($options));
        $cachekey = $this->_getCacheKey($key);

        $this->reCacheOnPublish($cachekey);

        if ('published' === $this->getVersion() && $this->cache && $cachedItem = $this->cacheGet($cachekey)) {
            $this->_assignState($cachedItem);
        } else {
            $options = array_merge($options, $this->getApiParameters());

            if ($this->resolveRelations) {
                $options['resolve_relations'] = $this->resolveRelations;
            }

            if ($this->language) {
                $options['language'] = $this->language;
            }

            if ($this->fallbackLanguage) {
                $options['fallback_lang'] = $this->fallbackLanguage;
            }

            if ($this->resolveLinks) {
                $options['resolve_links'] = $this->resolveLinks;
            }

            $response = $this->get($endpointUrl, $options);

            $this->_save($response, $cachekey, $this->getVersion());
        }

        return $this;
    }

    /**
     *  Sets global reference.
     *
     *  eg. global.global_referece
     *
     * @param mixed $reference
     *
     * @return $this
     */
    public function resolveRelations($reference)
    {
        $this->resolveRelations = $reference;
        $this->_relationsList = [];
        foreach (explode(',', $this->resolveRelations) as $relation) {
            $relationVars = explode('.', $relation);
            $this->_relationsList[$relationVars[0]] = $relationVars[1];
        }

        return $this;
    }

    /**
     * Set reference for how to resolve links.
     *
     * @param mixed $reference
     *
     * @return $this
     */
    public function resolveLinks($reference)
    {
        $this->resolveLinks = $reference;

        return $this;
    }

    /**
     * Gets a list of tags.
     *
     * array(
     *    'starts_with' => $slug
     * )
     *
     * @param array $options Options
     *
     * @return \Storyblok\Client
     */
    public function getTags($options = [])
    {
        $endpointUrl = 'tags/';

        $key = 'tags/' . serialize($options);
        $cachekey = $this->_getCacheKey($key);

        $this->reCacheOnPublish($key);

        if ('published' === $this->getVersion() && $this->cache && $cachedItem = $this->cacheGet($cachekey)) {
            $this->_assignState($cachedItem);
        } else {
            $options = array_merge($options, $this->getApiParameters());

            $response = $this->get($endpointUrl, $options);

            $this->_save($response, $cachekey, $this->getVersion());
        }

        return $this;
    }

    /**
     * Gets a list of datasource entries.
     *
     * @param string $slug    Slug
     * @param array  $options Options
     *
     * @return \Storyblok\Client
     */
    public function getDatasourceEntries($slug, $options = [])
    {
        $endpointUrl = 'datasource_entries/';

        $key = 'datasource_entries/' . $slug . '/' . serialize($options);
        $cachekey = $this->_getCacheKey($key);

        $this->reCacheOnPublish($key);

        if ('published' === $this->getVersion() && $this->cache && $cachedItem = $this->cacheGet($cachekey)) {
            $this->_assignState($cachedItem);
        } else {
            $options = array_merge(
                $options,
                ['datasource' => $slug],
                $this->getApiParameters()
            );

            $response = $this->get($endpointUrl, $options);

            $this->_save($response, $cachekey, $this->getVersion());
        }

        return $this;
    }

    /**
     * Gets a list of tags.
     *
     * array(
     *    'starts_with' => $slug
     * )
     *
     * @param array $options Options
     *
     * @return \Storyblok\Client
     */
    public function getLinks($options = [])
    {
        $key = $this->linksPath;
        $cachekey = $this->_getCacheKey($key);

        if ('published' === $this->getVersion() && $this->cache && $cachedItem = $this->cacheGet($cachekey)) {
            $this->_assignState($cachedItem);
        } else {
            $options = array_merge($options, $this->getApiParameters());

            $response = $this->get($key, $options);

            $this->_save($response, $cachekey, $this->getVersion());
        }

        return $this;
    }

    /**
     * Transforms datasources into a ['name']['value'] array.
     *
     * @return array
     */
    public function getAsNameValueArray()
    {
        if (!isset($this->responseBody)) {
            return [];
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
            return [];
        }

        $array = [];

        foreach ($this->responseBody['tags'] as $entry) {
            $array[] = $entry['name'];
        }

        return $array;
    }

    /**
     * Transforms links into a tree.
     *
     * @return array
     */
    public function getAsTree()
    {
        if (!isset($this->responseBody)) {
            return [];
        }

        $tree = [];

        foreach ($this->responseBody['links'] as $item) {
            if (!isset($tree[$item['parent_id']])) {
                $tree[$item['parent_id']] = [];
            }

            $tree[$item['parent_id']][] = $item;
        }

        return $this->_generateTree($tree, 0);
    }

    public function cacheClear()
    {
        $this->cache->clear();
    }

    /**
     * Gets a list of stories.
     *
     * @param string $slug   Slug
     * @param bool   $byUuid
     *
     * @return Client
     *
     * @throws ApiException
     */
    private function getStory($slug, $byUuid = false)
    {
        $key = 'stories/' . $slug;
        $cachekey = $this->_getCacheKey($key);

        $this->reCacheOnPublish($cachekey);
        if ('published' === $this->getVersion() && $this->cache && $cachedItem = $this->cacheGet($cachekey)) {
            if ($this->cacheNotFound && 404 === $cachedItem->httpResponseCode) {
                throw new ApiException(self::EXCEPTION_GENERIC_HTTP_ERROR, 404);
            }

            $this->_assignState($cachedItem);
        } else {
            $options = $this->getApiParameters();

            if ($byUuid) {
                $options['find_by'] = 'uuid';
            }

            if ($this->resolveRelations) {
                $options['resolve_relations'] = $this->resolveRelations;
            }

            if ($this->resolveLinks) {
                $options['resolve_links'] = $this->resolveLinks;
            }

            if ($this->release) {
                $options['from_release'] = $this->release;
            }

            if ($this->language) {
                $options['language'] = $this->language;
            }

            if ($this->fallbackLanguage) {
                $options['fallback_lang'] = $this->fallbackLanguage;
            }

            try {
                $response = $this->get($key, $options);
                $this->_save($response, $cachekey, $this->getVersion());
            } catch (\Exception $e) {
                if ($this->cacheNotFound && 404 === $e->getCode()) {
                    $result = new \stdClass();
                    $result->httpResponseBody = [];
                    $result->httpResponseCode = 404;
                    $result->httpResponseHeaders = [];
                    $this->cacheSave($result, $cachekey);
                }

                throw new ApiException(self::EXCEPTION_GENERIC_HTTP_ERROR . ' - ' . $e->getMessage(), $e->getCode());
            }
        }

        return $this;
    }

    /**
     * Automatically delete the cache of one item if client sends published parameter.
     *
     * @param string $key Cache key
     *
     * @return \Storyblok\Client
     */
    private function reCacheOnPublish($key)
    {
        if (isset($_GET['_storyblok_published']) && $this->cache) {
            $this->cache->delete($key);

            // Always refresh cache of links
            $linksCacheKey = $this->_getCacheKey($this->linksPath);
            $this->cache->delete($linksCacheKey);
            $this->setCacheVersion();
        }

        return $this;
    }

    /**
     * Recursive function to generate tree.
     *
     * @param array $items
     * @param int   $parent
     *
     * @return array
     */
    private function _generateTree($items, $parent = 0)
    {
        $tree = [];

        if (isset($items[$parent])) {
            $result = $items[$parent];

            foreach ($result as $item) {
                if (!isset($tree[$item['id']])) {
                    $tree[$item['id']] = [];
                }

                $tree[$item['id']]['item'] = $item;
                $tree[$item['id']]['children'] = $this->_generateTree($items, $item['id']);
            }
        }

        return $tree;
    }

    /**
     * Save's the current response in the cache if version is published.
     *
     * @param array|\stdClass $response
     * @param string          $key
     * @param string          $version
     */
    private function _save($response, $key, $version)
    {
        $this->_assignState($response);
        if ($this->cache
            && 'published' === $version
            && $response->httpResponseHeaders
            && 200 === $response->httpResponseCode) {
            $this->cacheSave($response, $key);
        }
    }

    private function cacheSave($value, string $key)
    {
        if ($this->cache) {
            $cacheItem = $this->cache->getItem($key);
            $cacheItem->set($value);

            return $this->cache->save($cacheItem);
        }

        return false;
    }

    private function cacheGet(string $key)
    {
        if ($this->cache) {
            return $this->cache->getItem($key)->get();
        }

        return false;
    }

    /**
     * Assigns the httpResponseBody and httpResponseHeader to '$this';.
     *
     * @param array  $response
     * @param string $key
     * @param string $version
     */
    private function _assignState($response)
    {
        $this->responseBody = $response->httpResponseBody;
        $this->responseHeaders = $response->httpResponseHeaders;
        $this->responseCode = $response->httpResponseCode;
    }

    /**
     * prepares to Options for the cache key. Fixes some issues for too long filenames if filecache is used.
     *
     * @param array  $response
     * @param string $key
     * @param string $version
     * @param mixed  $options
     */
    private function _prepareOptionsForKey($options)
    {
        $prepared = [];
        $keyOrder = [];
        foreach ($options as $key => $value) {
            $prepared[] = $value;
            $keyOrder[] = substr($key, 0, 1);
        }
        $prepared[] = implode('', $keyOrder);

        return $prepared;
    }

    private function _getCacheKey($key = '')
    {
        return hash('sha256', $key);
    }
}
