<?php

declare(strict_types=1);

namespace OpenPro\ConnectorSdk\Support;

use OpenPro\ConnectorSdk\Contracts\Connector;
use OpenPro\ConnectorSdk\Exceptions\ConfigValidationException;
use OpenPro\ConnectorSdk\Manifest\ConfigField;

abstract class AbstractScraperConnector implements Connector
{
    /** @param array<string, mixed> $config */
    public function validateConfig(array $config): void
    {
        $errors = [];

        foreach ($this->manifest()->configFields as $field) {
            $value = $config[$field->key] ?? null;
            if ($field->required && ($value === null || $value === '')) {
                $errors[] = "{$field->key} is required";
            }
        }

        if ($errors !== []) {
            throw new ConfigValidationException($errors);
        }
    }

    protected function sleepSeconds(float $seconds): void
    {
        if ($seconds <= 0) {
            return;
        }

        usleep((int) ($seconds * 1_000_000));
    }

    /** @param list<string> $items */
    protected function dedupe(array $items): array
    {
        $seen = [];
        $result = [];

        foreach ($items as $item) {
            $normalized = trim(preg_replace('/\s+/', ' ', $item) ?? $item);
            $key = mb_strtolower($normalized);
            if ($normalized === '' || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $result[] = $normalized;
        }

        return $result;
    }

    protected function matchesMetier(?string $haystack, string $needle): bool
    {
        if ($needle === '') {
            return true;
        }

        return $haystack !== null && mb_stripos($haystack, $needle) !== false;
    }
}
