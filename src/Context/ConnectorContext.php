<?php

declare(strict_types=1);

namespace OpenPro\ConnectorSdk\Context;

use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

final readonly class ConnectorContext
{
    /** @param array<string, mixed> $config
     *  @param array<string, mixed> $settings */
    public function __construct(
        public int $companyId,
        public int $installationId,
        public array $config,
        public array $settings,
        public ClientInterface $httpClient,
        public LoggerInterface $logger,
    ) {}

    public function configString(string $key, string $default = ''): string
    {
        $value = $this->config[$key] ?? $default;

        return is_string($value) ? trim($value) : $default;
    }

    public function configInt(string $key, int $default = 0): int
    {
        $value = $this->config[$key] ?? $default;

        return is_numeric($value) ? (int) $value : $default;
    }

    public function settingBool(string $key, bool $default = false): bool
    {
        return (bool) ($this->settings[$key] ?? $default);
    }
}
