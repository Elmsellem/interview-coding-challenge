<?php

namespace Elmsellem\Services;

use GuzzleHttp\{Client, HandlerStack, Middleware, RetryMiddleware};
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractHttpService
{
    protected Client $client;

    public function __construct(
        protected string $baseUri,
        protected int    $retryCount = 3,
        protected int    $retryDelay = 1000,
    ) {
        $this->client = $this->createClient();
    }

    protected function createClient(): Client
    {
        $stack = HandlerStack::create();

        $stack->push(Middleware::retry(
            $this->getRetryDecider(),
            fn ($retries) => RetryMiddleware::exponentialDelay($retries),
        ));

        return new Client([
            'base_uri' => $this->baseUri,
            'handler' => $stack,
        ]);
    }

    protected function getRetryDecider(): callable
    {
        return function (int $retries, RequestInterface $request, ResponseInterface $response = null) {
            $code = $response?->getStatusCode();
            $shouldRetry = in_array($code, [408, 429]) || ($code >= 500 && $code < 600);

            return $retries < $this->retryCount && $shouldRetry;
        };
    }

    protected function toDecodedJson(ResponseInterface $response): array
    {
        return json_decode($response->getBody()->getContents(), true) ?? [];
    }
}
