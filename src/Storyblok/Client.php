<?php

namespace Storyblok;

use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\PdoAdapter;
use Symfony\Component\Cache\CacheItem;

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
     * @var null|AbstractAdapter
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
     * List of resolved relations.
     *
     * @var array
     */
    private $resolvedRelations;
    /**
     * List of resolved relations.
     *
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
    public function setCache($driver, $options = []): self
    {
        $options['serializer'] = 'php';
        $options['prefix_key'] = 'storyblokcache';
        $options['prefix_tag'] = 'storyblokcachetag';
        $defaultLifetime = \array_key_exists('default_lifetime', $options) ?
            $options['default_lifetime'] : 0;

        switch ($driver) {
            case 'sqlite':
            case 'postgres':
            case 'mysql':
                $dbh = $options['pdo'];
                $this->cache = new PdoAdapter(
                    $dbh,
                    $options['prefix_key'],
                    $defaultLifetime,
                    $options
                );

                break;

            default:
                $options['directory'] = $options['path'];

                $this->cache = new FilesystemAdapter(
                    $options['prefix_key'],
                    $defaultLifetime,
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
        if ($this->isCache()) {
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
        if ($this->isCache()) {
            $this->cache->clear();
            $this->setCacheVersion();
        }

        return $this;
    }

    /**
     * Sets cache version to get a fresh version from cdn after clearing the cache.
     *
     * @param mixed $reset
     * @param mixed $injectValue
     *
     * @return \Storyblok\Client
     */
    public function setCacheVersion($reset = false, $injectValue = '')
    {
        if ($this->isCache()) {
            if ($reset) {
                $this->cv = '';
                $this->cache->delete(self::CACHE_VERSION_KEY);
            } else {
                if ('' === $injectValue) {
                    $res = $this->getStories(['per_page' => 1, 'version' => 'published']);
                    $this->cv = $res->responseBody['cv'];
                } else {
                    $this->cv = $injectValue;
                }

                $cacheItem = $this->cache->getItem(self::CACHE_VERSION_KEY);
                $cacheItem->set($this->cv);
                $this->cache->save($cacheItem);
            }
        }

        return $this;
    }

    /**
     * Gets cache version from cache or as timestamp.
     *
     * @return null|int
     */
    function getCacheVersion()
    {
        if (empty($this->cv)) {
            return null;
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
        $cacheKey = $this->_getCacheKey($key);

        $this->reCacheOnPublish($cacheKey);
        $cachedItem = $this->getCachedItem($cacheKey);

        if ($this->isPublishedVersion() && $cachedItem->isHit()) {
            $this->_assignState($cachedItem->get());
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

            if ($this->release) {
                $options['from_release'] = $this->release;
            }

            $response = $this->get($endpointUrl, $options);

            $this->_save($response, $cacheKey, $this->getVersion());
        }

        return $this;
    }

    /**
     *  Sets the list of the relations to be resolved.
     *
     *  eg. 'article-page.author'
     *
     * @param string $reference
     *
     * @return $this
     */
    public function resolveRelations($reference)
    {
        $this->resolveRelations = $reference;
        $this->_relationsList = [];
        foreach (explode(',', $this->resolveRelations) as $relation) {
            $relationVars = explode('.', $relation);
            if (!\array_key_exists($relationVars[0], $this->_relationsList)) {
                $this->_relationsList[$relationVars[0]] = [];
            }
            $this->_relationsList[$relationVars[0]][] = $relationVars[1];
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
        $cacheKey = $this->_getCacheKey($key);

        $this->reCacheOnPublish($key);

        $cachedItem = $this->getCachedItem($cacheKey);

        if ($this->isPublishedVersion() && $cachedItem->isHit()) {
            $this->_assignState($cachedItem->get());
        } else {
            $options = array_merge($options, $this->getApiParameters());

            $response = $this->get($endpointUrl, $options);

            $this->_save($response, $cacheKey, $this->getVersion());
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
        $cacheKey = $this->_getCacheKey($key);

        $this->reCacheOnPublish($key);

        $cachedItem = $this->getCachedItem($cacheKey);

        if ($this->isPublishedVersion() && $cachedItem->isHit()) {
            $this->_assignState($cachedItem->get());
        } else {
            $options = array_merge(
                $options,
                ['datasource' => $slug],
                $this->getApiParameters()
            );

            $response = $this->get($endpointUrl, $options);

            $this->_save($response, $cacheKey, $this->getVersion());
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
        $cacheKey = $this->_getCacheKey($key . serialize($this->_prepareOptionsForKey($options)));

        $cachedItem = $this->getCachedItem($cacheKey);

        if ($this->isPublishedVersion() && $cachedItem->isHit()) {
            $this->_assignState($cachedItem->get());
        } else {
            $options = array_merge($options, $this->getApiParameters());

            $response = $this->get($key, $options);

            $this->_save($response, $cacheKey, $this->getVersion());
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
        if ('' === $this->responseBody || [] === $this->responseBody) {
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
        if ('' === $this->responseBody || [] === $this->responseBody) {
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
        if ('' === $this->responseBody || [] === $this->responseBody) {
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

    public function getResolvedRelationByUuid($uuid)
    {
        if (\array_key_exists($uuid, $this->resolvedRelations)) {
            return $this->resolvedRelations[$uuid];
        }

        return false;
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
     * Enrich the Stories with resolved links and stories.
     *
     * @param array|\stdClass|string $data
     * @param mixed                  $level
     *
     * @return array|string
     */
    public function enrichContent($data, $level = 0)
    {
        $enrichedContent = $data;

        if (isset($data['component'])) {
            if (!$this->isStopResolving($level)) {
                foreach ($data as $fieldName => $fieldValue) {
                    if (isset($fieldValue['_stopResolving']) && $fieldValue['_stopResolving']) {
                        continue;
                    }

                    $enrichedContent[$fieldName] = $this->insertRelations($data['component'], $fieldName, $fieldValue);
                    $enrichedContent[$fieldName] = $this->insertLinks($enrichedContent[$fieldName]);
                    $enrichedContent[$fieldName] = $this->enrichContent($enrichedContent[$fieldName], $level + 1);
                }
            }
        } elseif (\is_array($data)) {
            if (!$this->isStopResolving($level)) {
                foreach ($data as $key => $value) {
                    if (\is_string($value) && \array_key_exists($value, $this->resolvedRelations)) {
                        if ('uuid' !== $key) {
                            $enrichedContent[$key] = $this->resolvedRelations[$value];
                        }
                    } else {
                        $enrichedContent[$key] = $this->enrichContent($value, $level + 1);
                    }
                }
            }
        }

        return $enrichedContent;
    }

    public function responseHandler($responseObj, $queryString = []): Response
    {
        $result = parent::responseHandler($responseObj, $queryString);

        if (\is_array($result->httpResponseBody) && isset($result->httpResponseBody['story']) || isset($result->httpResponseBody['stories'])) {
            $result->httpResponseBody = $this->enrichStories($result->httpResponseBody, $queryString);
        }

        return $result;
    }

    /**
     * Return true if published content is requested.
     */
    public function isPublishedVersion(): bool
    {
        return 'published' === $this->getVersion();
    }

    public function getCachedItem(string $key)
    {
        if ($this->isCache()) {
            try {
                return $this->cache->getItem($key);
            } catch (InvalidArgumentException $e) {
            }
        }

        return new CacheItem();
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
        $cacheKey = $this->_getCacheKey($key);

        $this->reCacheOnPublish($cacheKey);
        $cachedItem = $this->getCachedItem($cacheKey);

        if ($this->isPublishedVersion() && $cachedItem->isHit()) {
            if ($this->cacheNotFound && 404 === $cachedItem->get()->getCode()) {
                throw new ApiException(self::EXCEPTION_GENERIC_HTTP_ERROR, 404);
            }

            $this->_assignState($cachedItem->get());
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
                $this->_save($response, $cacheKey, $this->getVersion());
            } catch (\Exception $e) {
                if ($this->cacheNotFound && 404 === $e->getCode()) {
                    $result = new \stdClass();
                    $result->httpResponseBody = [];
                    $result->httpResponseCode = 404;
                    $result->httpResponseHeaders = [];
                    $this->cacheSave($result, $cacheKey);
                }

                throw new ApiException(self::EXCEPTION_GENERIC_HTTP_ERROR . ' - ' . $e->getMessage(), $e->getCode());
            }
        }

        return $this;
    }

    private function enrichStories($data, $queryString)
    {
        $enrichedData = $data;
        $this->getResolvedRelations($data, $queryString);
        $this->getResolvedLinks($data, $queryString);

        if (isset($data['story'])) {
            // if (isset($enrichedData['rel_uuids'])) {
            //     $enrichedData['rels'] = $this->resolvedRelations;
            // }
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

        if (!empty($data['rels'])) {
            foreach ($data['rels'] as $index => $rel) {
                $enrichedData['rels'][$index] = $this->enrichContent($rel, -1);
            }
        }

        if (!empty($data['links'])) {
            foreach ($data['links'] as $index => $rel) {
                $enrichedData['links'][$index] = $this->enrichContent($rel, -1);
            }
        }

        return $enrichedData;
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
        if (isset($this->_relationsList[$component]) && \in_array($field, $this->_relationsList[$component], true)) {
            if (\is_string($value)) {
                if (isset($this->resolvedRelations[$value])) {
                    $filteredNode = $this->resolvedRelations[$value];
                    $this->settingStopResolving($filteredNode);
                }
            } elseif (\is_array($value)) {
                $filteredNodeTemp = [];
                $resolved = false;

                foreach ($value as $item) {
                    if (\is_string($item) && isset($this->resolvedRelations[$item])) {
                        $resolved = true;
                        $story = $this->resolvedRelations[$item];
                        $this->settingStopResolving($story);
                        $filteredNodeTemp[] = $story;
                    }
                }

                if ($resolved) {
                    $filteredNode = $filteredNodeTemp;
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

    /**
     * Automatically delete the cache of one item if client sends published parameter.
     *
     * @param string $key Cache key
     *
     * @return \Storyblok\Client
     */
    private function reCacheOnPublish($key)
    {
        if (isset($_GET['_storyblok_published']) && $this->isCache()) {
            $this->cache->delete($key);

            // Always refresh cache of links
            $linksCacheKey = $this->_getCacheKey($this->linksPath);
            $this->cache->delete($linksCacheKey);
            $this->setCacheVersion(true);
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
     * @param Response $response
     * @param string   $key
     * @param string   $version
     */
    private function _save($response, $key, $version)
    {
        $this->_assignState($response);
        if ($this->isCache()
            && 'published' === $version
            && $response->httpResponseHeaders
            && 200 === $response->httpResponseCode) {
            $this->cacheSave($response, $key);
        }
    }

    private function isCache(): bool
    {
        return (null !== $this->cache) && ($this->cache instanceof AbstractAdapter);
    }

    private function cacheSave($value, string $key)
    {
        if ($this->isCache()) {
            $cacheItem = $this->cache->getItem($key);
            $cacheItem->set($value);
            if ('object' === \gettype($value) && 'Storyblok\\Response' === \get_class($value) && \is_array($value->getBody()) && \array_key_exists('cv', $value->getBody())) {
                // $cachedCv = $this->cache->getItem(self::CACHE_VERSION_KEY);
                // $cachedCv->set($value->getBody()['cv']);
                $this->setCacheVersion(false, $value->getBody()['cv']);
                // $this->cache->save($cachedCv);
            }

            return $this->cache->save($cacheItem);
        }

        return false;
    }

    private function cacheGet(string $key)
    {
        if ($this->isCache()) {
            return $this->cache->getItem($key)->get();
        }

        return false;
    }

    /**
     * Assigns the httpResponseBody and httpResponseHeader to '$this';.
     */
    private function _assignState(Response $response)
    {
        $this->responseBody = $response->httpResponseBody;
        $this->responseHeaders = $response->httpResponseHeaders;
        $this->responseCode = $response->httpResponseCode;
    }

    /**
     * prepares to Options for the cache key. Fixes some issues for too long filenames if filecache is used.
     *
     * @param mixed $options
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

    private function settingStopResolving(&$data)
    {
        $data['_stopResolving'] = true;
    }

    private function isStopResolving($level)
    {
        return $level > 4;
    }
}
