<?php

declare(strict_types=1);

namespace OpenPro\ConnectorSdk\Client;

use OpenPro\ConnectorSdk\Exceptions\OpenProHttpException;
use OpenPro\ConnectorSdk\Offer\NormalizedOffer;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class OpenProClient
{
    public function __construct(
        private readonly ClientInterface $http,
        private readonly RequestFactoryInterface $requests,
        private readonly StreamFactoryInterface $streams,
        private readonly OpenProClientOptions $options,
    ) {}

    /** @return array<string, mixed> */
    public function publishJob(NormalizedOffer $offer, bool $draft = true): array
    {
        return $this->send('POST', '/job_posts', $offer->toJobPostPayload($draft));
    }

    /** @return list<array<string, mixed>> */
    public function listTokens(): array
    {
        $payload = $this->send('GET', '/developer/tokens');

        /** @var list<array<string, mixed>> $tokens */
        $tokens = $payload['tokens'] ?? [];

        return $tokens;
    }

    /** @return array<string, mixed> */
    public function createToken(string $name): array
    {
        $payload = $this->send('POST', '/developer/tokens', ['name' => $name]);

        /** @var array<string, mixed> $token */
        $token = $payload['token'] ?? $payload;

        return $token;
    }

    public function revokeToken(int $id): void
    {
        $this->send('DELETE', '/developer/tokens/'.$id);
    }

    /** @param array<string, mixed>|null $body
     *  @return array<string, mixed> */
    private function send(string $method, string $path, ?array $body = null): array
    {
        $response = $this->http->sendRequest($this->buildRequest($method, $path, $body));

        return $this->decode($response);
    }

    /** @param array<string, mixed>|null $body */
    private function buildRequest(string $method, string $path, ?array $body): RequestInterface
    {
        $request = $this->requests->createRequest($method, $this->uri($path))
            ->withHeader('Authorization', 'Bearer '.$this->options->token)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Accept-Language', $this->options->language)
            ->withHeader('X-Language', $this->options->language);

        if ($body === null) {
            return $request;
        }

        $encoded = json_encode($body + ['language' => $this->options->language], JSON_THROW_ON_ERROR);

        return $request
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streams->createStream($encoded));
    }

    private function uri(string $path): string
    {
        $base = rtrim($this->options->baseUrl, '/');
        $query = http_build_query(['language' => $this->options->language]);

        return $base.$path.'?'.$query;
    }

    /** @return array<string, mixed> */
    private function decode(ResponseInterface $response): array
    {
        $raw = (string) $response->getBody();
        $status = $response->getStatusCode();
        if ($status >= 400) {
            throw new OpenProHttpException('OpenPro API request failed.', $status, $raw);
        }

        if ($raw === '') {
            return ['success' => true];
        }

        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            throw new OpenProHttpException('OpenPro API returned invalid JSON.', $status, $raw);
        }

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }
}
