<?php

namespace Storyblok;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

/**
 * Storyblok Client.
 */
class BaseClient
{
    public const SDK_VERSION = '2.6.1';

    public const EXCEPTION_GENERIC_HTTP_ERROR = 'An HTTP Error has occurred!';

    public const DEFAULT_PER_PAGE = 25;

    /**
     * @var array|string
     */
    public $responseBody;

    /**
     * @var array
     */
    public $responseHeaders;

    /**
     * @var int
     */
    public $responseCode;

    /**
     * @var int
     */
    protected $maxRetries = 5;

    /**
     * @var Guzzle
     */
    protected $client;

    /**
     * @var array|string
     */
    protected $proxy;

    /**
     * @var float
     */
    protected $timeout;

    /**
     * @var array
     */
    protected $_relationsList;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @param null|string $apiEndpoint
     * @param bool        $ssl
     */
    public function __construct(?string $apiKey = null, $apiEndpoint = null, string $apiVersion = 'v2', $ssl = false, ?string $apiRegion = null)
    {
        $handlerStack = HandlerStack::create(new CurlHandler());
        $handlerStack->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));

        $this->setApiKey($apiKey);
        $this->client = new Guzzle([
            'base_uri' => $this->generateEndpoint($apiEndpoint, $apiVersion, $ssl, $apiRegion),
            'handler' => $handlerStack,
            'headers' => [
                'SB-Agent' => 'SB-PHP',
                'SB-Agent-Version' => static::SDK_VERSION,
            ],
        ]);
    }

    /**
     * @param mixed $version
     *
     * @return $this
     */
    public function mockable(array $mocks, $version = 'v2'): self
    {
        $handlerStack = HandlerStack::create(new MockHandler($mocks));
        $handlerStack->push(Middleware::retry($this->retryDecider(), static function (): int {
            return 0;
        }));

        $this->client = new Guzzle([
            'base_uri' => sprintf('http://api.storyblok.com/%s/cdn/', $version),
            'handler' => $handlerStack,
        ]);

        return $this;
    }

    public function retryDecider(): \Closure
    {
        return function (
            $retries,
            $request,
            $response = null,
            ?TransferException $exception = null
        ): bool {
            // Limit the number of retries
            if ($retries >= $this->maxRetries) {
                return false;
            }

            // Retry connection exceptions
            if ($exception instanceof ConnectException) {
                return true;
            }

            // Retry on server errors
            return $response && ($response->getStatusCode() >= 500 || 429 === $response->getStatusCode());
        };
    }

    /**
     * delay 1s 2s 3s 4s 5s.
     *
     * @return \Closure
     */
    public function retryDelay()
    {
        return static function ($numberOfRetries) {
            return 1000 * $numberOfRetries;
        };
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param mixed $maxRetries
     */
    public function setMaxRetries($maxRetries): self
    {
        $this->maxRetries = $maxRetries;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxRetries()
    {
        return $this->maxRetries;
    }

    /**
     * @param array|string $proxy see http://docs.guzzlephp.org/en/stable/request-options.html#proxy for possible values
     */
    public function setProxy($proxy): self
    {
        $this->proxy = $proxy;

        return $this;
    }

    /**
     * @return array|string
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * set timeout in seconds.
     *
     * @param float $timeout
     */
    public function setTimeout($timeout): self
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
     * @param string $endpointUrl
     * @param array  $queryString
     *
     * @throws ApiException
     */
    public function get($endpointUrl, $queryString = []): Response
    {
        try {
            $query = http_build_query($queryString, '', '&');
            $string = preg_replace('/%5B(?:\d|[1-9]\d+)%5D=/', '=', $query);
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
        } catch (ClientException $clientException) {
            throw new ApiException(self::EXCEPTION_GENERIC_HTTP_ERROR . ' - ' . $clientException->getMessage(), $clientException->getCode());
        }
    }

    /**
     * Uses the `get()` method, but with included pagination handling.
     *
     * @return array array of responses or array of items (for examples Stories),
     *               according with the $returnResponsesArray param
     *
     * @throws ApiException
     */
    public function getAll(string $endpointUrl, array $queryString = [], bool $returnResponsesArray = false): array
    {
        $queryString['per_page'] = $queryString['per_page'] ?? self::DEFAULT_PER_PAGE;
        $queryString['page'] = 1;

        $firstResponse = $this->get($endpointUrl, $queryString);

        $perPage = $firstResponse->httpResponseHeaders['Per-Page'][0] ?? null;
        $totalRecords = $firstResponse->httpResponseHeaders['Total'][0] ?? null;

        $allResponses[] = clone $firstResponse;

        if (!(null === $perPage || null === $totalRecords || $totalRecords <= $perPage)) {
            $lastPage = (int) ceil($totalRecords / $perPage);
            foreach (range(2, $lastPage) as $page) {
                $queryString['page'] = $page;
                $nextResponse = $this->get($endpointUrl, $queryString);
                $allResponses[] = clone $nextResponse;
            }
        }

        if ($returnResponsesArray) {
            return $allResponses;
        }

        $stories = [];
        foreach ($allResponses as $response) {
            array_push($stories, ...$response->httpResponseBody['stories']);
        }

        return $stories;
    }

    /**
     * @param ResponseInterface $responseObj
     * @param array             $queryString
     */
    public function responseHandler($responseObj, $queryString = []): Response
    {
        $result = new Response();
        $result->setCode($responseObj->getStatusCode());
        $result->setHeaders($responseObj->getHeaders());
        $result->setBodyFromStreamInterface($responseObj->getBody());

        $data = (string) $responseObj->getBody();
        $jsonResponseData = (array) json_decode($data, true);

        // return response data as json if possible, raw if not
        $result->httpResponseBody = $data && [] === $jsonResponseData ? $data : $jsonResponseData;

        /*
        if (\is_array($result->httpResponseBody) && isset($result->httpResponseBody['story']) || isset($result->httpResponseBody['stories'])) {
            $result->httpResponseBody = $this->enrichStories($result->httpResponseBody, $queryString);
        }
        */

        return $result;
    }

    /**
     * Gets the json response body.
     *
     * @return array|\stdClass
     */
    public function getBody()
    {
        // if (isset($this->responseBody)) {
        if ([] !== $this->responseBody && '' !== $this->responseBody) {
            return $this->responseBody;
        }

        return [];
    }

    /**
     * Gets the response headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        if ([] !== $this->responseHeaders) {
            return $this->responseHeaders;
        }

        return [];
    }

    /**
     * Gets the response status.
     *
     * @return int
     */
    public function getCode()
    {
        return $this->responseCode;
    }

    /**
     * @return string
     */
    protected function getResponseExceptionMessage(ResponseInterface $responseObj)
    {
        $body = (string) $responseObj->getBody();
        $response = json_decode($body);
        if (JSON_ERROR_NONE !== json_last_error()) {
            return '';
        }

        if (!isset($response->message)) {
            return '';
        }

        return $response->message;
    }

    /**
     * @param string $apiEndpoint
     * @param bool   $ssl
     */
    private function generateEndpoint($apiEndpoint, string $apiVersion, $ssl, ?string $apiRegion): string
    {
        $prefix = $this instanceof ManagementClient ? '' : '/cdn';

        $protocol = $ssl ? 'https://' : 'http://';

        if (!$apiEndpoint) {
            $region = $apiRegion ? '-' . $apiRegion : '';
            $apiEndpoint = sprintf('api%s.storyblok.com', $region);
        }

        return $protocol . $apiEndpoint . '/' . $apiVersion . $prefix . '/';
    }
}
