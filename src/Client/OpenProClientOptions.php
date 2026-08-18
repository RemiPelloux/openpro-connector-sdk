<?php

declare(strict_types=1);

namespace OpenPro\ConnectorSdk\Client;

use OpenPro\ConnectorSdk\Exceptions\OpenProHttpException;

final readonly class OpenProClientOptions
{
    public const DEFAULT_BASE_URL = 'https://api.openpro.ai/api';

    public function __construct(
        public string $token,
        public string $baseUrl = self::DEFAULT_BASE_URL,
        public string $language = 'en',
    ) {
        if (trim($token) === '') {
            throw new OpenProHttpException('API token is required.');
        }
    }
}
