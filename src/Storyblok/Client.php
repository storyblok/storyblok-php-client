<?php

namespace Storyblok;

use Apix\Cache as ApixCache;

/**
 * Storyblok Client.
 */
class Client extends BaseClient
{
    const CACHE_VERSION_KEY = 'storyblok:cv';
    const EXCEPTION_GENERIC_HTTP_ERROR = 'An HTTP Error has occurred!';

    /**
     * @var string
     */
    public $cacheVersion;

    /**
     * @var Cache
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
     * @var array
     */
    private $resolvedLinks;

    /**
     * @var bool
     */
    private $cacheNotFound;
    /**
     * @var null|mixed
     */
    private $cv;
    /**
     * @var array
     */
    private $_relationsList;

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

        $this->cv = $this->cache->load(self::CACHE_VERSION_KEY);

        if (!$this->cv) {
            $this->setCacheVersion();
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

        if ($this->cache) {
            $this->cache->delete($key);

            // Always refresh cache of links
            $this->cache->delete($this->linksPath);
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
            $this->cache->flush();
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
            $this->cache->save($this->cv, self::CACHE_VERSION_KEY);
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
     * @throws ApiException
     *
     * @return Client
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
     * @throws ApiException
     *
     * @return Client
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

        $this->reCacheOnPublish($key);

        if ('published' === $this->getVersion() && $this->cache && $cachedItem = $this->cache->load($cachekey)) {
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
     * Retrieve or resolve the relations.
     *
     * @param \stdClass $data
     * @param array     $queryString
     */
    function getResolvedRelations($data, $queryString)
    {
        $this->resolvedRelations = [];
        $relations = [];

        if (isset($data['rels'])) {
            $relations = $data['rels'];
        } elseif (isset($data['rel_uuids'])) {
            $relSize = \count($data['rel_uuids']);
            $chunks = [];
            $chunkSize = 50;

            for ($i = 0; $i < $relSize; $i += $chunkSize) {
                $end = min($relSize, $i + $chunkSize);
                $chunks[] = \array_slice($data['rel_uuids'], $i, $end);
            }

            for ($chunkIndex = 0; $chunkIndex < \count($chunks); ++$chunkIndex) {
                $relationsParams = [
                    'per_page' => $chunkSize,
                    'by_uuids' => implode(',', $chunks[$chunkIndex]),
                ];
                if (isset($queryString['language'])) {
                    $relationsParams['language'] = $queryString['language'];
                }
                $relationsRes = $this->getStories($relationsParams);

                $relations = array_merge($relations, $relationsRes->responseBody['stories']);
            }
        }

        foreach ($relations as $rel) {
            $this->resolvedRelations[$rel['uuid']] = $rel;
        }
    }

    /**
     * Retrieve or resolve the Links.
     *
     * @param \stdClass $data
     */
    function getResolvedLinks($data, array $queryString)
    {
        $this->resolvedLinks = [];
        $links = [];

        if (isset($data['links'])) {
            $links = $data['links'];
        } elseif (isset($data['link_uuids'])) {
            $linksSize = \count($data['link_uuids']);
            $chunks = [];
            $chunkSize = 50;

            for ($i = 0; $i < $linksSize; $i += $chunkSize) {
                $end = min($linksSize, $i + $chunkSize);
                $chunks[] = \array_slice($data['link_uuids'], $i, $end);
            }

            for ($chunkIndex = 0; $chunkIndex < \count($chunks); ++$chunkIndex) {
                $linksRes = $this->getStories([
                    'per_page' => $chunkSize,
                    'language' => isset($queryString['language']) ? $queryString['language'] : 'default',
                    'by_uuids' => implode(',', $chunks[$chunkIndex]),
                ]);

                $links = array_merge($links, $linksRes->responseBody['stories']);
            }
        }
        foreach ($links as $link) {
            $this->resolvedLinks[$link['uuid']] = $link;
        }
    }

    /**
     *  Sets global reference.
     *
     *  eg. global.global_referece
     *
     * @param $reference
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
     * Enrich the Stories with resolved links and stories.
     *
     * @param \stdClass
     * @param mixed $data
     *
     * @return \stdClass
     */
    function enrichContent($data)
    {
        $enrichedContent = $data;

        if (\is_array($data) && isset($data['component'])) {
            if (!isset($story['_stopResolving'])) {
                foreach ($data as $fieldName => $fieldValue) {
                    $enrichedContent[$fieldName] = $this->insertRelations($data['component'], $fieldName, $fieldValue);
                    $enrichedContent[$fieldName] = $this->insertLinks($enrichedContent[$fieldName]);
                    $enrichedContent[$fieldName] = $this->enrichContent($enrichedContent[$fieldName]);
                }
            }
        } elseif (\is_array($data)) {
            foreach ($data as $key => $value) {
                $enrichedContent[$key] = $this->enrichContent($value);
            }
        }

        return $enrichedContent;
    }

    /**
     * Set reference for how to resolve links.
     *
     * @param $reference
     *
     * @return $this
     */
    public function resolveLinks($reference)
    {
        $this->resolveLinks = $reference;

        return $this;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $responseObj
     * @param array                               $queryString
     *
     * @return \stdClass
     */
    public function responseHandler($responseObj, $queryString = [])
    {
        $result = parent::responseHandler($responseObj, $queryString);

        if (\is_array($result->httpResponseBody) && isset($result->httpResponseBody['story']) || isset($result->httpResponseBody['stories'])) {
            $result->httpResponseBody = $this->enrichStories($result->httpResponseBody, $queryString);
        }

        return $result;
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

        if ('published' === $this->getVersion() && $this->cache && $cachedItem = $this->cache->load($cachekey)) {
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

        if ('published' === $this->getVersion() && $this->cache && $cachedItem = $this->cache->load($cachekey)) {
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

        if ('published' === $this->getVersion() && $this->cache && $cachedItem = $this->cache->load($cachekey)) {
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

    /**
     * Insert the resolved relations in a story.
     *
     * @param string       $component
     * @param string       $field
     * @param array|string $value
     */
    private function insertRelations($component, $field, $value)
    {
        $filteredNode = $value;
        if (isset($this->_relationsList[$component]) && $field === $this->_relationsList[$component]) {
            if (\is_string($value)) {
                if (isset($this->resolvedRelations[$value])) {
                    $filteredNode = $this->resolvedRelations[$value];
                    $filteredNode['_stopResolving'] = true;
                }
            } elseif (\is_array($value)) {
                $filteredNode = [];
                foreach ($value as $item) {
                    if (\is_string($item) && isset($this->resolvedRelations[$item])) {
                        $story = $this->resolvedRelations[$item];
                        $story['_stopResolving'] = true;
                        $filteredNode[] = $story;
                    }
                }
            }
        }

        return $filteredNode;
    }

    /**
     * Insert the resolved links in a story.
     *
     * @param \stdClass $node
     *
     * @return \stdClass
     */
    private function insertLinks($node)
    {
        $filteredNode = $node;
        if (isset($node['fieldtype']) && 'multilink' === $node['fieldtype'] && 'story' === $node['linktype']) {
            if (isset($node['id']) && \is_string($node['id']) && isset($this->resolvedLinks[$node['id']])) {
                $filteredNode['story'] = $this->resolvedLinks[$node['id']];
            } elseif (isset($node['uuid']) && \is_string($node['uuid']) && isset($this->resolvedLinks[$node['uuid']])) {
                $filteredNode['story'] = $this->resolvedLinks[$node['uuid']];
            }
        }

        return $filteredNode;
    }

    private function enrichStories($data, $queryString)
    {
        $enrichedData = $data;
        $this->getResolvedRelations($data, $queryString);
        $this->getResolvedLinks($data, $queryString);

        if (isset($data['story'])) {
            $enrichedData['story']['content'] = $this->enrichContent($data['story']['content']);
        } elseif (isset($data['stories'])) {
            $stories = [];
            foreach ($data['stories'] as $index => $story) {
                $story = $data['stories'][$index];
                $story['content'] = $this->enrichContent($story['content']);
                $stories[] = $story;
            }
            $enrichedData['stories'] = $stories;
        }

        return $enrichedData;
    }

    /**
     * Gets a list of stories.
     *
     * @param string $slug   Slug
     * @param bool   $byUuid
     *
     * @throws ApiException
     *
     * @return Client
     */
    private function getStory($slug, $byUuid = false)
    {
        $key = 'stories/' . $slug;
        $cachekey = $this->_getCacheKey($key);

        $this->reCacheOnPublish($key);

        if ('published' === $this->getVersion() && $this->cache && $cachedItem = $this->cache->load($cachekey)) {
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

                    $this->cache->save($result, $cachekey);
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
            $this->cache->delete($this->linksPath);
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
     * @param array  $response
     * @param string $key
     * @param string $version
     */
    private function _save($response, $key, $version)
    {
        $this->_assignState($response);

        if ($this->cache
            && 'published' === $version
            && $response->httpResponseHeaders
            && 200 === $response->httpResponseCode) {
            $this->cache->save($response, $key);
        }
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
