<?php

declare(strict_types=1);

namespace OpenPro\ConnectorSdk\Exceptions;

final class OpenProHttpException extends ConnectorException
{
    public function __construct(
        string $message,
        public readonly int $status = 0,
        public readonly ?string $body = null,
    ) {
        parent::__construct($message);
    }
}
