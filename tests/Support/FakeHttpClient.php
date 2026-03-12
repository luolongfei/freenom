<?php

declare(strict_types=1);

namespace Luolongfei\Tests\Support;

use GuzzleHttp\Psr7\Response;

final class FakeHttpClient
{
    public array $requests = [];

    private array $queue = [
        'get' => [],
        'post' => [],
    ];

    public function queue(string $method, $response): void
    {
        $method = strtolower($method);
        $this->queue[$method][] = $response;
    }

    public function get(string $url, array $options = [])
    {
        return $this->dequeue('get', $url, $options);
    }

    public function post(string $url, array $options = [])
    {
        return $this->dequeue('post', $url, $options);
    }

    public static function jsonResponse(array $data, int $status = 200): Response
    {
        return new Response($status, ['Content-Type' => 'application/json'], json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public static function textResponse(string $body, int $status = 200): Response
    {
        return new Response($status, [], $body);
    }

    private function dequeue(string $method, string $url, array $options)
    {
        $this->requests[] = [
            'method' => strtoupper($method),
            'url' => $url,
            'options' => $options,
        ];

        if (empty($this->queue[$method])) {
            throw new \RuntimeException(sprintf('No queued %s response for %s', strtoupper($method), $url));
        }

        $response = array_shift($this->queue[$method]);
        if ($response instanceof \Throwable) {
            throw $response;
        }

        return $response;
    }
}
