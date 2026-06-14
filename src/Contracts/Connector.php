<?php

declare(strict_types=1);

namespace OpenPro\ConnectorSdk\Contracts;

use OpenPro\ConnectorSdk\Context\ConnectorContext;
use OpenPro\ConnectorSdk\Manifest\ConnectorManifest;
use OpenPro\ConnectorSdk\Offer\NormalizedOffer;

interface Connector
{
    public function key(): string;

    public function manifest(): ConnectorManifest;

    /** @param array<string, mixed> $config */
    public function validateConfig(array $config): void;

    /** @return iterable<NormalizedOffer> */
    public function fetchOffers(ConnectorContext $context): iterable;
}
