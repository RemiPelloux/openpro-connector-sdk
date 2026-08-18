<?php

declare(strict_types=1);

namespace OpenPro\ConnectorSdk\Tests\Unit;

use Nyholm\Psr7\Factory\Psr17Factory;
use OpenPro\ConnectorSdk\Client\OpenProClient;
use OpenPro\ConnectorSdk\Client\OpenProClientOptions;
use OpenPro\ConnectorSdk\Exceptions\OpenProHttpException;
use OpenPro\ConnectorSdk\Offer\NormalizedOffer;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class OpenProClientTest extends TestCase
{
    public function test_publish_job_sends_bearer_language_and_payload(): void
    {
        $factory = new Psr17Factory();
        $http = new RecordingHttpClient($factory->createResponse(201)->withBody(
            $factory->createStream('{"success":true,"id":9}'),
        ));

        $client = new OpenProClient($http, $factory, $factory, new OpenProClientOptions(
            token: 'op_live_test',
            language: 'fr',
        ));

        $result = $client->publishJob($this->offer(), draft: true);

        self::assertSame(9, $result['id']);
        $request = $http->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('Bearer op_live_test', $request->getHeaderLine('Authorization'));
        self::assertSame('fr', $request->getHeaderLine('X-Language'));
        self::assertStringContainsString('language=fr', (string) $request->getUri());
        $body = json_decode((string) $request->getBody(), true);
        self::assertSame('draft', $body['status']);
        self::assertSame('Backend engineer', $body['title']);
        self::assertSame('fr', $body['language']);
    }

    public function test_create_token_returns_plaintext_once(): void
    {
        $factory = new Psr17Factory();
        $http = new RecordingHttpClient($factory->createResponse(201)->withBody(
            $factory->createStream('{"success":true,"token":{"plain_text":"secret"}}'),
        ));
        $client = new OpenProClient($http, $factory, $factory, new OpenProClientOptions('op_live_test'));

        $token = $client->createToken('Cursor');

        self::assertSame('secret', $token['plain_text']);
        self::assertSame('POST', $http->lastRequest()->getMethod());
    }

    public function test_failed_response_raises_http_exception(): void
    {
        $factory = new Psr17Factory();
        $http = new RecordingHttpClient($factory->createResponse(401)->withBody(
            $factory->createStream('{"message":"Unauthenticated."}'),
        ));
        $client = new OpenProClient($http, $factory, $factory, new OpenProClientOptions('bad'));

        $this->expectException(OpenProHttpException::class);
        $client->listTokens();
    }

    public function test_options_reject_empty_token(): void
    {
        $this->expectException(OpenProHttpException::class);
        new OpenProClientOptions('  ');
    }

    private function offer(): NormalizedOffer
    {
        return NormalizedOffer::fromArray([
            'external_id' => 'gh-12',
            'source_url' => 'https://boards.greenhouse.io/jobs/12',
            'title' => 'Backend engineer',
            'content' => 'Ship the API.',
            'location' => 'Paris',
        ]);
    }
}

final class RecordingHttpClient implements ClientInterface
{
    private ?RequestInterface $request = null;

    public function __construct(private readonly ResponseInterface $response) {}

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->request = $request;

        return $this->response;
    }

    public function lastRequest(): RequestInterface
    {
        if ($this->request === null) {
            throw new \RuntimeException('No request was sent.');
        }

        return $this->request;
    }
}
