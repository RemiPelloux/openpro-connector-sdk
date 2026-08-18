<?php

declare(strict_types=1);

namespace OpenPro\ConnectorSdk\Tests\Unit;

use InvalidArgumentException;
use OpenPro\ConnectorSdk\Offer\NormalizedOffer;
use PHPUnit\Framework\TestCase;

final class NormalizedOfferFactoryTest extends TestCase
{
    public function test_from_array_maps_fields_and_defaults_currency(): void
    {
        $offer = NormalizedOffer::fromArray([
            'external_id' => 'gh-12',
            'source_url' => 'https://boards.greenhouse.io/jobs/12',
            'title' => 'Backend engineer',
            'content' => 'Ship the API.',
            'location' => 'Paris',
            'latitude' => '48.85',
            'contract_type' => 'CDI',
            'main_missions' => ['Build connectors', 12, ''],
            'weekly_schedule' => ['monday' => 'office'],
        ]);

        self::assertSame('gh-12', $offer->externalId);
        self::assertSame('EUR', $offer->currency);
        self::assertSame(48.85, $offer->latitude);
        self::assertSame(['Build connectors'], $offer->mainMissions);
        self::assertSame(['monday' => 'office'], $offer->weeklySchedule);
        self::assertSame('active', $offer->toJobPostPayload(draft: false)['status']);
    }

    public function test_from_array_rejects_missing_title(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('title');

        NormalizedOffer::fromArray([
            'external_id' => 'x',
            'source_url' => 'https://example.com/job',
            'title' => '   ',
            'content' => 'Body',
            'location' => 'Lyon',
        ]);
    }
}
