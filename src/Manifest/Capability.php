<?php

declare(strict_types=1);

namespace OpenPro\ConnectorSdk\Manifest;

enum Capability: string
{
    case DailySync = 'daily_sync';
    case DraftImport = 'draft_import';
    case AiVideo = 'ai_video';
    case AiImage = 'ai_image';
    case AutoCloseMissing = 'auto_close_missing';
    case CandidateImport = 'candidate_import';

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
