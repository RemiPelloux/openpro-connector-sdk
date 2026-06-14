<?php

declare(strict_types=1);

namespace OpenPro\ConnectorSdk\Examples;

use OpenPro\ConnectorSdk\Context\ConnectorContext;
use OpenPro\ConnectorSdk\Manifest\Capability;
use OpenPro\ConnectorSdk\Manifest\ConfigField;
use OpenPro\ConnectorSdk\Manifest\ConnectorManifest;
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
        $context->logger->info('Example connector fetch', ['installation' => $context->installationId]);

        return [];
    }
}
