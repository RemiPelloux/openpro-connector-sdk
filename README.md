# OpenPro Connector SDK

Framework-agnostic PHP package for building OpenPro connectors.

## Install

```bash
composer require openpro/connector-sdk
```

## Quick start

Implement `OpenPro\ConnectorSdk\Contracts\Connector`:

```php
final class MyConnector extends AbstractScraperConnector
{
    public function key(): string
    {
        return 'my-connector';
    }

    public function manifest(): ConnectorManifest
    {
        return ConnectorManifest::create(
            name: 'My Connector',
            description: 'Sync jobs from My ATS',
            configFields: [
                ConfigField::string('api_key', required: true),
            ],
            capabilities: [Capability::DailySync, Capability::DraftImport],
        );
    }

    /** @return iterable<NormalizedOffer> */
    public function fetchOffers(ConnectorContext $context): iterable
    {
        // fetch and yield NormalizedOffer instances
    }
}
```

See [docs/SDK.md](docs/SDK.md) for the full developer guide.
