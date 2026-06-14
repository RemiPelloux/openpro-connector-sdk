<?php

declare(strict_types=1);

namespace OpenPro\ConnectorSdk\Exceptions;

final class ConfigValidationException extends ConnectorException
{
    /** @param list<string> $errors */
    public function __construct(
        public readonly array $errors,
    ) {
        parent::__construct('Connector configuration is invalid: '.implode(', ', $errors));
    }
}
