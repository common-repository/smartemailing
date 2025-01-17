<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api;

use SmartemailingDeps\GuzzleHttp\Client;
use SmartemailingDeps\GuzzleHttp\Exception\GuzzleException;
use SmartemailingDeps\JetBrains\PhpStorm\Pure;
use SmartemailingDeps\Psr\Http\Message\ResponseInterface;
use SmartemailingDeps\SmartEmailing\Api\Model\Response\BaseResponse;
use SmartemailingDeps\SmartEmailing\Exception\RequestException;
use SmartemailingDeps\SmartEmailing\SmartEmailing;
use SmartemailingDeps\SmartEmailing\Util\Helpers;
abstract class AbstractApi
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_PATCH = 'PATCH';
    public const METHOD_DELETE = 'DELETE';
    protected const URI_PREFIX = '/api/v3/';
    protected int $chunkLimit = 500;
    private Client $client;
    #[Pure]
    public function __construct(SmartEmailing $smartEmailing)
    {
        $this->client = $smartEmailing->getClient();
    }
    protected function get(string $uri, array $params = []) : ResponseInterface
    {
        return $this->queryRequest(self::METHOD_GET, $uri, $params);
    }
    protected function post(string $uri, array $params = []) : ResponseInterface
    {
        return $this->jsonRequest(self::METHOD_POST, $uri, $params);
    }
    protected function put(string $uri, array $params = []) : ResponseInterface
    {
        return $this->jsonRequest(self::METHOD_PUT, $uri, $params);
    }
    protected function patch(string $uri, array $params = []) : ResponseInterface
    {
        return $this->jsonRequest(self::METHOD_PATCH, $uri, $params);
    }
    protected function delete(string $uri, array $params = []) : ResponseInterface
    {
        return $this->queryRequest(self::METHOD_DELETE, $uri, $params);
    }
    protected function queryRequest(string $method, string $uri, array $params = []) : ResponseInterface
    {
        return $this->request($method, $uri, ['query' => $params]);
    }
    protected function jsonRequest(string $method, string $uri, array $params = []) : ResponseInterface
    {
        return $this->request($method, $uri, ['json' => $params]);
    }
    protected function request(string $method, string $uri, array $options = []) : ResponseInterface
    {
        try {
            return $this->getClient()->request($method, self::URI_PREFIX . $uri, $options);
        } catch (GuzzleException $exception) {
            /** @var \GuzzleHttp\Exception\RequestException $exception */
            $response = new BaseResponse($exception->getResponse());
            $message = $exception->getMessage();
            if ($message === 'Client error' && \is_string($response->getMessage())) {
                $message = "Client error: {$response->getMessage()}";
            }
            throw new RequestException($response, $exception->getRequest(), $message, \intval($exception->getCode()), $exception);
        }
    }
    protected function replaceUrlParameters(string $uri, array $parameters) : string
    {
        return Helpers::replaceUrlParameters($uri, $parameters);
    }
    protected function getClient() : Client
    {
        return $this->client;
    }
    #[Pure]
    protected static function encodePath(string $uri) : string
    {
        return \rawurlencode($uri);
    }
}
