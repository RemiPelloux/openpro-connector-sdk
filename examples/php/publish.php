<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use OpenPro\ConnectorSdk\Client\OpenProClient;
use OpenPro\ConnectorSdk\Client\OpenProClientOptions;
use OpenPro\ConnectorSdk\Offer\NormalizedOffer;

$factory = new HttpFactory();
$client = new OpenProClient(
    new Client(),
    $factory,
    $factory,
    new OpenProClientOptions(
        token: (string) getenv('OPENPRO_API_TOKEN'),
        language: 'en',
    ),
);

$offer = NormalizedOffer::fromArray([
    'external_id' => 'gh-12',
    'source_url' => 'https://boards.greenhouse.io/jobs/12',
    'title' => 'Backend engineer',
    'content' => 'Ship the API.',
    'location' => 'Paris',
    'contract_type' => 'CDI',
    'currency' => 'EUR',
]);

$client->publishJob($offer, draft: true);
