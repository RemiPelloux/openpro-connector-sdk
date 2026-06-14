<?php

declare(strict_types=1);

namespace OpenPro\ConnectorSdk\Manifest;

final readonly class ConfigField
{
    /** @param array<string, mixed> $meta */
    private function __construct(
        public string $key,
        public string $type,
        public bool $required,
        public ?string $label,
        public mixed $default,
        public array $meta,
    ) {}

    public static function string(
        string $key,
        bool $required = false,
        ?string $label = null,
        ?string $default = null,
    ): self {
        return new self($key, 'string', $required, $label, $default, []);
    }

    public static function integer(
        string $key,
        bool $required = false,
        ?string $label = null,
        ?int $default = null,
    ): self {
        return new self($key, 'integer', $required, $label, $default, []);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'key' => $this->key,
            'type' => $this->type,
            'required' => $this->required,
            'label' => $this->label,
            'default' => $this->default,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
