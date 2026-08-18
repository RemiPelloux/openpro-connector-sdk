<?php

declare(strict_types=1);

namespace OpenPro\ConnectorSdk\Offer;

final readonly class NormalizedOffer
{
    /** @param list<string> $mainMissions
     *  @param list<string> $prerequisites
     *  @param list<string> $technicalSkills
     *  @param list<string> $advantages
     *  @param array<string, mixed> $source */
    public function __construct(
        public string $externalId,
        public string $sourceUrl,
        public string $title,
        public string $content,
        public string $location,
        public ?float $latitude,
        public ?float $longitude,
        public string $contractType,
        public string $remuneration,
        public string $remunerationType,
        public string $currency,
        public ?float $hourlyRate,
        public string $hourlyPrimes,
        public string $workStartTime,
        public string $workEndTime,
        public string $lunchStartTime,
        public string $lunchEndTime,
        /** @var array<string, string> */
        public array $weeklySchedule,
        public array $mainMissions,
        public array $prerequisites,
        public array $technicalSkills,
        public array $advantages,
        public ?string $metier = null,
        public array $source = [],
    ) {
        self::requireNonEmpty([
            'external_id' => $this->externalId,
            'source_url' => $this->sourceUrl,
            'title' => $this->title,
            'content' => $this->content,
            'location' => $this->location,
        ]);
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            externalId: self::string($data, 'external_id'),
            sourceUrl: self::string($data, 'source_url'),
            title: self::string($data, 'title'),
            content: self::string($data, 'content'),
            location: self::string($data, 'location'),
            latitude: self::nullableFloat($data, 'latitude'),
            longitude: self::nullableFloat($data, 'longitude'),
            contractType: self::string($data, 'contract_type'),
            remuneration: self::string($data, 'remuneration'),
            remunerationType: self::string($data, 'remuneration_type'),
            currency: self::string($data, 'currency', 'EUR'),
            hourlyRate: self::nullableFloat($data, 'hourly_rate'),
            hourlyPrimes: self::string($data, 'hourly_primes'),
            workStartTime: self::string($data, 'work_start_time'),
            workEndTime: self::string($data, 'work_end_time'),
            lunchStartTime: self::string($data, 'lunch_start_time'),
            lunchEndTime: self::string($data, 'lunch_end_time'),
            weeklySchedule: self::stringMap($data, 'weekly_schedule'),
            mainMissions: self::stringList($data, 'main_missions'),
            prerequisites: self::stringList($data, 'prerequisites'),
            technicalSkills: self::stringList($data, 'technical_skills'),
            advantages: self::stringList($data, 'advantages'),
            metier: self::nullableString($data, 'metier'),
            source: is_array($data['source'] ?? null) ? $data['source'] : [],
        );
    }

    /** @return array<string, mixed> */
    public function toJobPostPayload(bool $draft = true): array
    {
        return [
            'status' => $draft ? 'draft' : 'active',
            'title' => $this->title,
            'content' => $this->content,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'contract_type' => $this->contractType,
            'remuneration' => $this->remuneration,
            'remuneration_type' => $this->remunerationType,
            'currency' => $this->currency,
            'hourly_rate' => $this->hourlyRate,
            'hourly_primes' => $this->hourlyPrimes,
            'work_start_time' => $this->workStartTime,
            'work_end_time' => $this->workEndTime,
            'lunch_start_time' => $this->lunchStartTime,
            'lunch_end_time' => $this->lunchEndTime,
            'weekly_schedule' => $this->weeklySchedule,
            'main_missions' => $this->mainMissions,
            'prerequisites' => $this->prerequisites,
            'technical_skills' => $this->technicalSkills,
            'advantages' => $this->advantages,
            'source_url' => $this->sourceUrl,
        ];
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'external_id' => $this->externalId,
            'source_url' => $this->sourceUrl,
            'title' => $this->title,
            'content' => $this->content,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'contract_type' => $this->contractType,
            'remuneration' => $this->remuneration,
            'remuneration_type' => $this->remunerationType,
            'currency' => $this->currency,
            'hourly_rate' => $this->hourlyRate,
            'hourly_primes' => $this->hourlyPrimes,
            'work_start_time' => $this->workStartTime,
            'work_end_time' => $this->workEndTime,
            'lunch_start_time' => $this->lunchStartTime,
            'lunch_end_time' => $this->lunchEndTime,
            'weekly_schedule' => $this->weeklySchedule,
            'main_missions' => $this->mainMissions,
            'prerequisites' => $this->prerequisites,
            'technical_skills' => $this->technicalSkills,
            'advantages' => $this->advantages,
            'metier' => $this->metier,
            'source' => $this->source,
        ];
    }

    /** @param array<string, string> $fields */
    private static function requireNonEmpty(array $fields): void
    {
        $missing = [];
        foreach ($fields as $key => $value) {
            if (trim($value) === '') {
                $missing[] = $key;
            }
        }

        if ($missing !== []) {
            throw new \InvalidArgumentException('Missing required offer fields: '.implode(', ', $missing));
        }
    }

    /** @param array<string, mixed> $data */
    private static function string(array $data, string $key, string $default = ''): string
    {
        $value = $data[$key] ?? $default;

        return is_scalar($value) ? trim((string) $value) : $default;
    }

    /** @param array<string, mixed> $data */
    private static function nullableString(array $data, string $key): ?string
    {
        $value = self::string($data, $key);

        return $value === '' ? null : $value;
    }

    /** @param array<string, mixed> $data */
    private static function nullableFloat(array $data, string $key): ?float
    {
        $value = $data[$key] ?? null;

        return is_numeric($value) ? (float) $value : null;
    }

    /** @param array<string, mixed> $data
     *  @return list<string> */
    private static function stringList(array $data, string $key): array
    {
        $value = $data[$key] ?? [];
        if (! is_array($value)) {
            return [];
        }

        $items = [];
        foreach ($value as $item) {
            if (is_string($item) && trim($item) !== '') {
                $items[] = trim($item);
            }
        }

        return $items;
    }

    /** @param array<string, mixed> $data
     *  @return array<string, string> */
    private static function stringMap(array $data, string $key): array
    {
        $value = $data[$key] ?? [];
        if (! is_array($value)) {
            return [];
        }

        $map = [];
        foreach ($value as $mapKey => $item) {
            if (is_string($mapKey) && is_scalar($item)) {
                $map[$mapKey] = trim((string) $item);
            }
        }

        return $map;
    }
}
