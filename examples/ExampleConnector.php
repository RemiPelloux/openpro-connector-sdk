<?php

declare(strict_types=1);

namespace OpenPro\ConnectorSdk\Examples;

use OpenPro\ConnectorSdk\Context\ConnectorContext;
use OpenPro\ConnectorSdk\Manifest\Capability;
use OpenPro\ConnectorSdk\Manifest\ConfigField;
use OpenPro\ConnectorSdk\Manifest\ConnectorManifest;
use OpenPro\ConnectorSdk\Offer\NormalizedOffer;
use OpenPro\ConnectorSdk\Support\AbstractScraperConnector;

/** @codeCoverageIgnore */
final class ExampleConnector extends AbstractScraperConnector
{
    public function key(): string
    {
        return 'example-connector';
    }

    public function manifest(): ConnectorManifest
    {
        return ConnectorManifest::create(
            name: 'Example Connector',
            description: 'Starter template for OpenPro connector developers.',
            configFields: [
                ConfigField::string('endpoint', required: true, label: 'API endpoint'),
            ],
            capabilities: [Capability::DailySync, Capability::DraftImport],
        );
    }

    public function fetchOffers(ConnectorContext $context): iterable
    {
        $endpoint = $context->configString('endpoint');
        $context->logger->info('Example connector fetch', [
            'installation' => $context->installationId,
            'endpoint' => $endpoint,
        ]);

        yield NormalizedOffer::fromArray([
            'external_id' => 'example-1',
            'source_url' => rtrim($endpoint, '/').'/jobs/1',
            'title' => 'Example role',
            'content' => 'Replace this with a mapped ATS offer.',
            'location' => 'Paris',
            'contract_type' => 'CDI',
            'currency' => 'EUR',
        ]);
    }
}
