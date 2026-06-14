<?php

declare(strict_types=1);

namespace OpenPro\ConnectorSdk\Tests\Unit;

use OpenPro\ConnectorSdk\Manifest\Capability;
use OpenPro\ConnectorSdk\Manifest\ConfigField;
use OpenPro\ConnectorSdk\Manifest\ConnectorManifest;
use OpenPro\ConnectorSdk\Offer\NormalizedOffer;
use PHPUnit\Framework\TestCase;

final class ConnectorManifestTest extends TestCase
{
    public function test_manifest_exports_schema_and_capabilities(): void
    {
        $manifest = ConnectorManifest::create(
            name: 'Test',
            description: 'Desc',
            configFields: [ConfigField::string('city', required: true)],
            capabilities: [Capability::DailySync],
        );

        self::assertSame('Test', $manifest->name);
        self::assertCount(1, $manifest->configSchema());
        self::assertSame(['daily_sync'], $manifest->capabilityValues());
    }
}

final class NormalizedOfferTest extends TestCase
{
    public function test_job_post_payload_includes_source_url(): void
    {
        $offer = new NormalizedOffer(
            externalId: 'abc',
            sourceUrl: 'https://example.com/job/abc',
            title: 'Developer',
            content: 'Build things',
            location: 'Paris',
            latitude: 48.85,
            longitude: 2.35,
            contractType: 'CDI',
            remuneration: '45000',
            remunerationType: 'monthly',
            currency: '€',
            hourlyRate: null,
            hourlyPrimes: '',
            workStartTime: '09:00',
            workEndTime: '18:00',
            lunchStartTime: '12:30',
            lunchEndTime: '13:30',
            weeklySchedule: ['monday' => 'desk'],
            mainMissions: ['Code'],
            prerequisites: ['PHP'],
            technicalSkills: ['Laravel'],
            advantages: ['Remote'],
        );

        $payload = $offer->toJobPostPayload(draft: true);
        self::assertSame('draft', $payload['status']);
        self::assertSame('https://example.com/job/abc', $payload['source_url']);
    }
}
