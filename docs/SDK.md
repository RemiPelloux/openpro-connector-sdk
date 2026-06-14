# OpenPro Connector SDK — Developer Guide

## Overview

The OpenPro Connector SDK lets you build integrations that pull external job offers (and later candidates) into OpenPro. Connectors implement a single interface and return `NormalizedOffer` objects.

## Core types

| Type | Role |
| --- | --- |
| `Connector` | Contract: `key()`, `manifest()`, `validateConfig()`, `fetchOffers()` |
| `ConnectorManifest` | Metadata + config schema + capabilities |
| `ConnectorContext` | Runtime: company/installation ids, config, settings, HTTP client, logger |
| `NormalizedOffer` | Canonical job shape consumed by OpenPro import pipeline |

## Building a connector

1. Extend `AbstractScraperConnector` (or implement `Connector` directly).
2. Define config fields in `manifest()` using `ConfigField::string()` / `ConfigField::integer()`.
3. Declare capabilities: `DailySync`, `DraftImport`, `AiVideo`, `AiImage`, `AutoCloseMissing`.
4. In `fetchOffers()`, yield one `NormalizedOffer` per external job.
5. Register the class in the OpenPro backend `ConnectorRegistry`.

## NormalizedOffer fields

Mirror the OpenPro `POST /job_posts` payload: title, content, location, coordinates, contract, remuneration, schedule arrays, missions, skills, advantages, and `source_url`.

## Settings (installation-level)

Recruiters configure import behaviour per installation:

- `draft_only` — create posts as draft
- `generate_video` — queue Sora video (requires credits)
- `generate_ai_image` — generate hero image (requires credits)
- `auto_close_missing` — close imported posts when source offer disappears

## Testing locally

```bash
composer install
./vendor/bin/phpunit
```

## Private distribution

This package is published to the private `RemiPelloux/openpro-connector-sdk` repository and consumed by `openpro-backend` via Composer path/VCS repository.
