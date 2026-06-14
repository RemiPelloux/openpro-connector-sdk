<?php

declare(strict_types=1);

namespace OpenPro\ConnectorSdk\Manifest;

final readonly class ConnectorManifest
{
    /** @param list<ConfigField> $configFields
     *  @param list<Capability> $capabilities */
    private function __construct(
        public string $name,
        public string $description,
        public string $vendor,
        public string $category,
        public string $version,
        public array $configFields,
        public array $capabilities,
    ) {}

    /** @param list<ConfigField> $configFields
     *  @param list<Capability> $capabilities */
    public static function create(
        string $name,
        string $description,
        array $configFields = [],
        array $capabilities = [],
        string $vendor = 'OpenPro',
        string $category = 'jobs',
        string $version = '1.0.0',
    ): self {
        return new self($name, $description, $vendor, $category, $version, $configFields, $capabilities);
    }

    /** @return list<array<string, mixed>> */
    public function configSchema(): array
    {
        return array_map(static fn (ConfigField $field): array => $field->toArray(), $this->configFields);
    }

    /** @return list<string> */
    public function capabilityValues(): array
    {
        return array_map(static fn (Capability $cap): string => $cap->value, $this->capabilities);
    }
}
