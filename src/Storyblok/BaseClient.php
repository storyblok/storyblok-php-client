<?php

namespace Storyblok;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

/**
* Storyblok Client
*/
class BaseClient
{
    const EXCEPTION_GENERIC_HTTP_ERROR = "An HTTP Error has occurred!";

    /**
     * @var stdClass
     */
    public $responseBody;

    /**
     * @var stdClass
     */
    public $responseHeaders;

    /**
     * @var integer
     */
    public $responseCode;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var integer
     */
    protected $maxRetries = 5;

    /**
     * @var Guzzle
     */
    protected $client;

    /**
     * @var string|array
     */
    protected $proxy;

    /**
     * @var float
     */
    protected $timeout;

    /**
     * @param string $apiKey
     * @param string $apiEndpoint
     * @param string $apiVersion
     * @param bool   $ssl
     */
    function __construct($apiKey = null, $apiEndpoint = "api.storyblok.com", $apiVersion = "v2", $ssl = false)
    {
        $handlerStack = HandlerStack::create(new CurlHandler());
        $handlerStack->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));

        $this->setApiKey($apiKey);
        $this->client = new Guzzle([
            'base_uri'=> $this->generateEndpoint($apiEndpoint, $apiVersion, $ssl),
            'handler' => $handlerStack
        ]);
    }

    public function retryDecider()
    {
        return function (
            $retries,
            $request,
            $response = null,
            TransferException $exception = null
        ) {
            // Limit the number of retries
            if ($retries >= $this->maxRetries) {
                return false;
            }

            // Retry connection exceptions
            if ($exception instanceof ConnectException) {
                return true;
            }

            if ($response) {
                // Retry on server errors
                if ($response->getStatusCode() >= 500 || $response->getStatusCode() == 429) {
                    return true;
                }
            }

            return false;
        };
    }

    /**
     * delay 1s 2s 3s 4s 5s
     *
     * @return Closure
     */
    public function retryDelay()
    {
        return function ($numberOfRetries) {
            return 1000 * $numberOfRetries;
        };
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param $maxRetries
     * @return BaseClient
     */
    public function setMaxRetries($maxRetries)
    {
        $this->maxRetries = $maxRetries;
        return $this;
    }

    /**
     * @param string|array $proxy see http://docs.guzzlephp.org/en/stable/request-options.html#proxy for possible values
     * @return Client
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
        return $this;
    }

    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * set timeout in seconds
     *
     * @param float $timeout
     * @return BaseClient
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @return float
     */
    public function getTimeout()
    {
        return $this->timeout;
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
        if ($this instanceof ManagementClient) {
            $prefix = "";
        } else {
            $prefix = "/cdn";
        }

        if (!$ssl) {
            $protocol = "http://";
        } else {
            $protocol = "https://";
        }

        return $protocol . $apiEndpoint . "/" . $apiVersion . $prefix . "/";
    }

    /**
     * @param string $endpointUrl
     * @param array  $queryString
     *
     * @return \stdClass
     *
     * @throws ApiException
     */
    public function get($endpointUrl, $queryString = array())
    {
        try {
            $query = http_build_query($queryString, null, '&');
            $string = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $query);
            $string = preg_replace('/%5B__or%5D%5B\d+%5D%/', '%5B__or%5D%5B%5D%', $string);
            $requestOptions = [RequestOptions::QUERY => $string];

            if ($this->getProxy()) {
                $requestOptions[RequestOptions::PROXY] = $this->getProxy();
            }

            if ($this->getTimeout()) {
                $requestOptions[RequestOptions::TIMEOUT] = $this->getTimeout();
            }

            if ($this instanceof ManagementClient) {
                $requestOptions[RequestOptions::HEADERS] = ['Authorization' => $this->apiKey];
            }
            $responseObj = $this->client->request('GET', $endpointUrl, $requestOptions);

            return $this->responseHandler($responseObj, $queryString);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            throw new ApiException(self::EXCEPTION_GENERIC_HTTP_ERROR . ' - ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $responseObj
     *
     * @return \stdClass
     */
    public function responseHandler($responseObj, $queryString = [])
    {
        $httpResponseCode = $responseObj->getStatusCode();
        $data = (string) $responseObj->getBody();
        $jsonResponseData = (array) json_decode($data, true);
        $result = new \stdClass();

        // return response data as json if possible, raw if not
        $result->httpResponseBody = $data && empty($jsonResponseData) ? $data : $jsonResponseData;
        $result->httpResponseCode = $httpResponseCode;
        $result->httpResponseHeaders = $responseObj->getHeaders();
        if (is_array($result->httpResponseBody) && isset($result->httpResponseBody['story']) || isset($result->httpResponseBody['stories'])) {
            $result->httpResponseBody = $this->enrichStories($result->httpResponseBody, $queryString);
        }
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
     * Gets the response status
     *
     * @return array
     */
    public function getCode()
    {
        return $this->responseCode;
    }

    private function enrichStories($data, $queryString) {
        $enrichedData = $data;
        $this->getResolvedRelations($data, $queryString);
        $this->getResolveLinks($data, $queryString);
        
        if(isset($data['story'])) {
            $enrichedData['story']['content'] = $this->enrichContent($data['story']['content']);
        } else if(isset($data['stories'])) {
            $stories = [];
            foreach($data['stories'] as $index => $story) {
                $story = $data['stories'][$index];
                $story['content'] = $this->enrichContent($story['content']);
                $stories[] = $story;
            }
            $enrichedData['stories'] = $stories;
        }
        return $enrichedData;
    }

    function enrichContent($data) {
        $enrichedContent = $data;
        if (empty($data)) {
            return '';
        }

        if (is_array($data) && isset($data['component'])) {
            if(!isset($story['_stopResolving'])) {
                foreach($data as $fieldName => $fieldValue) {
                    $enrichedContent[$fieldName] = $this->insertRelations($data['component'], $fieldName, $fieldValue);
                    $enrichedContent[$fieldName] = $this->insertLinks($enrichedContent[$fieldName]);
                    $enrichedContent[$fieldName] = $this->enrichContent($enrichedContent[$fieldName]);
                }        
            }
        } else if (is_array($data)) {
            foreach($data as $key => $value) {
                $enrichedContent[$key] = $this->enrichContent($value);
            }
        }

        return $enrichedContent;
    }

    function getResolvedRelations($data, $queryString) {
        $this->resolvedRelations = [];
        $relations = [];

        if(isset($data['rels'])) {
            $relations = $data['rels'];
        } else if(isset($data['rel_uuids'])) {
            $relSize = count($data['rel_uuids']);
            $chunks = [];
            $chunkSize = 50;
      
            for ($i = 0; $i < $relSize; $i += $chunkSize) {
              $end = min($relSize, $i + $chunkSize);
              $chunks[] = array_slice($data['rel_uuids'], $i, $end);
            }
      
            for ($chunkIndex = 0; $chunkIndex < count($chunks); $chunkIndex++) {
                $relationsRes = $this->getStories(array(
                  'per_page' => $chunkSize,
                  'language' => isset($queryString['language']) ? $queryString['language'] : 'default',
                  'by_uuids' => implode(',', $chunks[$chunkIndex])
                ));
                
                $relations = array_merge($relations, $relationsRes->responseBody['stories']);
            }
        }

        foreach($relations as $rel) {
            $this->resolvedRelations[$rel['uuid']] = $rel;    
        }
    }

    function getResolveLinks($data) {
        $this->resolvedLinks = [];
        $links = [];

        if(isset($data['links'])) {
            $links = $data['links'];
        } else if(isset($data['link_uuids'])) {
            $linksSize = count($data['link_uuids']);
            $chunks = [];
            $chunkSize = 50;
      
            for ($i = 0; $i < $linksSize; $i += $chunkSize) {
              $end = min($linksSize, $i + $chunkSize);
              $chunks[] = array_slice($data['link_uuids'], $i, $end);
            }
      
            for ($chunkIndex = 0; $chunkIndex < count($chunks); $chunkIndex++) {
                $linksRes = $this->getStories(array(
                  'per_page' => $chunkSize,
                  'language' => isset($queryString['language']) ? $queryString['language'] : 'default',
                  'by_uuids' => implode(',', $chunks[$chunkIndex])
                ));
                
                $links = array_merge($links, $linksRes->responseBody['stories']);
            }
        }

        foreach($data['links'] as $link) {
            $this->resolvedLinks[$link['uuid']] = $link;    
        }
    }
    
    private function insertRelations($component, $field, $value) {
        $filteredNode = $value;
        if(isset($this->_relationsList[$component]) && $field == $this->_relationsList[$component]) {
            if(is_string($value)) {
                if(isset($this->resolvedRelations[$value])) {
                    $filteredNode = $this->resolvedRelations[$value];
                    $filteredNode['_stopResolving'] = true;
                }
            } else if(is_array($value)) {
                $filteredNode = [];
                foreach($value as $item) {
                    if(isset($this->resolvedRelations[$item])) {
                        $story = $this->resolvedRelations[$item];
                        $story['_stopResolving'] = true;
                        $filteredNode[] = $story;
                    }
                }
            }
        }
        return $filteredNode;
    }
    
    private function insertLinks($node) {
        $filteredNode = $node;
        if(isset($node['fieldtype']) && $node['fieldtype'] == 'multilink' && $node['linktype'] == 'story') {
            if(isset($node['id']) && is_string($node['id']) && isset($this->resolvedLinks[$node['id']])) {
                $filteredNode['story'] = $this->resolvedLinks[$node['id']];
            } else if(isset($node['uuid']) && is_string($node['uuid']) && isset($this->resolvedLinks[$node['uuid']])) {
                $filteredNode['story'] = $this->resolvedLinks[$node['uuid']];
            }
        }
        return $filteredNode;
    }
}
