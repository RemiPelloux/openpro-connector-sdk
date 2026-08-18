# OpenPro Connector SDK — Developer Guide

Translations: [English](../README.md) · [Français](i18n/README.fr.md) · [Español](i18n/README.es.md) · [Deutsch](i18n/README.de.md) · [Italiano](i18n/README.it.md) · [Português](i18n/README.pt.md) · [Polski](i18n/README.pl.md) · [Nederlands](i18n/README.nl.md)

## Overview

The OpenPro Connector SDK lets you pull jobs from an ATS into [OpenPro](https://openpro.ai). Use the REST API from any language, or the PHP package to map `NormalizedOffer` objects and publish them.

Public repository: https://github.com/RemiPelloux/openpro-connector-sdk

## Core types

| Type | Role |
| --- | --- |
| `OpenProClient` | Publish jobs and manage developer API keys |
| `OpenProClientOptions` | Token, API base URL, `language` |
| `Connector` | Contract: `key()`, `manifest()`, `validateConfig()`, `fetchOffers()` |
| `ConnectorManifest` | Metadata + config schema + capabilities |
| `ConnectorContext` | Runtime: company/installation ids, config, settings, HTTP client, logger |
| `NormalizedOffer` | Canonical job shape (`fromArray()`, `toJobPostPayload()`) |

## Building a connector

1. Extend `AbstractScraperConnector` (or implement `Connector` directly).
2. Define config fields in `manifest()` using `ConfigField::string()` / `ConfigField::integer()`.
3. Declare capabilities: `DailySync`, `DraftImport`, `AiVideo`, `AiImage`, `AutoCloseMissing`.
4. In `fetchOffers()`, yield `NormalizedOffer::fromArray(...)` per external job.
5. Publish with `OpenProClient::publishJob()` or register the class in OpenPro.

## NormalizedOffer fields

Required: `external_id`, `source_url`, `title`, `content`, `location`.

Also mapped: coordinates, contract, remuneration, schedule arrays, missions, skills, advantages, `metier`, `source`.

`toJobPostPayload()` mirrors `POST https://api.openpro.ai/api/job_posts`.

## Settings (installation-level)

- `draft_only` — create posts as draft
- `generate_video` — queue Sora video (requires credits)
- `generate_ai_image` — generate hero image (requires credits)
- `auto_close_missing` — close imported posts when the source offer disappears

## Language

Send `language` on every request (`en`, `fr`, `es`, `de`, `it`, `pt`, `pl`, `nl`, …). The PHP client also sets `Accept-Language` and `X-Language`.

## Tests

```bash
composer install
./vendor/bin/phpunit
```

Mock the PSR HTTP client. Do not hit a live ATS or the OpenPro API in unit tests.
