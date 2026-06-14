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
    ) {}

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
}
