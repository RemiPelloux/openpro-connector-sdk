# OpenPro Connector SDK

PHP library to pull jobs from a European ATS (Workday, SmartRecruiters, Teamtailor, Greenhouse, …) into [OpenPro](https://openpro.ai).

**Repository:** https://github.com/RemiPelloux/openpro-connector-sdk

Recruiters create API keys and follow per-ATS guides in **Settings → ATS connection**:

| Environment | Page |
|---|---|
| Production | https://openpro.ai/settings?section=ats-connexion |
| Development | https://dev.openpro.ai/settings?section=ats-connexion |

---

## Wiki

1. [What this SDK does](#1-what-this-sdk-does)
2. [Create an API key](#2-create-an-api-key)
3. [Authenticate REST calls](#3-authenticate-rest-calls)
4. [Connect MCP](#4-connect-mcp)
5. [Install the SDK](#5-install-the-sdk)
6. [Build a connector](#6-build-a-connector)
7. [NormalizedOffer](#7-normalizedoffer)
8. [ATS in Europe](#8-ats-in-europe)
9. [Test locally](#9-test-locally)
10. [Support](#10-support)

---

## 1. What this SDK does

OpenPro is the destination. Your ATS stays the source of truth for requisitions.

```
ATS API  →  Connector (this SDK)  →  OpenPro import  →  jobs, pipeline, Mia, MCP
```

Implement one `Connector`. Yield `NormalizedOffer` objects. OpenPro publishes them as job posts (draft or live) and can attach AI photo/video.

Related surfaces:

| Surface | URL |
|---|---|
| REST API | `https://api.openpro.ai/api` |
| MCP (Streamable HTTP) | `https://mcp.openpro.ai` |
| OAuth consent | `https://openpro.ai/oauth/mcp/consent` |

---

## 2. Create an API key

1. Sign in as a recruiter.
2. Open **Settings → ATS connection → API keys**.
3. Name the key (for example `Cursor` or `Greenhouse sync`).
4. Copy the secret immediately. OpenPro stores only a hash.

Limits: 10 keys per account. Revoke unused keys from the same modal.

---

## 3. Authenticate REST calls

```http
Authorization: Bearer <api_key>
Accept: application/json
```

Useful routes while building a connector:

| Method | Path | Purpose |
|---|---|---|
| `GET` | `/developer/tokens` | List keys (no secrets) |
| `POST` | `/developer/tokens` | Create a key |
| `DELETE` | `/developer/tokens/{id}` | Revoke |
| `POST` | `/job_posts` | Publish a normalized offer (backend import path) |

Never commit the key. Use env (`OPENPRO_API_TOKEN`).

---

## 4. Connect MCP

OpenPro MCP exposes the same assistant tools as Mia (jobs, pipeline, messages, applications).

### Remote (Cursor, Claude Desktop)

Add the MCP server `https://mcp.openpro.ai` and complete OAuth on `/oauth/mcp/consent`.

### Local stdio

```json
{
  "mcpServers": {
    "openpro": {
      "command": "node",
      "args": ["/absolute/path/to/openpro-mcp-service/dist/stdio.js"],
      "env": {
        "OPENPRO_MCP_TOKEN": "<api_key>",
        "OPENPRO_API_URL": "https://api.openpro.ai/api"
      }
    }
  }
}
```

The API key created in Settings works as `OPENPRO_MCP_TOKEN` (ability `mcp`). Remote OAuth remains the recommended path for hosted clients.

Most ATS vendors do **not** ship an official MCP server. Use OpenPro MCP plus this SDK to sync their jobs.

---

## 5. Install the SDK

```bash
composer require openpro/connector-sdk
```

Private VCS (same GitHub repo):

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/RemiPelloux/openpro-connector-sdk"
    }
  ]
}
```

---

## 6. Build a connector

```php
final class GreenhouseConnector extends AbstractScraperConnector
{
    public function key(): string
    {
        return 'greenhouse';
    }

    public function manifest(): ConnectorManifest
    {
        return ConnectorManifest::create(
            name: 'Greenhouse',
            description: 'Sync jobs from Greenhouse Harvest',
            configFields: [
                ConfigField::string('api_key', required: true),
            ],
            capabilities: [Capability::DailySync, Capability::DraftImport],
        );
    }

    /** @return iterable<NormalizedOffer> */
    public function fetchOffers(ConnectorContext $context): iterable
    {
        // call the ATS API, yield NormalizedOffer instances
    }
}
```

Register the class in `openpro-backend` `ConnectorRegistry`. Recruiters then install it from **Settings → Connectors**.

See [docs/SDK.md](docs/SDK.md) for field-level notes.

---

## 7. NormalizedOffer

Mirror `POST /job_posts`: title, content, location, coordinates, contract, remuneration, schedule, missions, skills, advantages, `source_url`.

Installation settings:

- `draft_only`
- `generate_video`
- `generate_ai_image`
- `auto_close_missing`

---

## 8. ATS in Europe

Guides live in-product (logo + MCP/API steps):

Workday, SAP SuccessFactors, Oracle Recruiting, Greenhouse, SmartRecruiters, Teamtailor, Personio, softgarden, Recruitee, Workable, Bullhorn, iCIMS, Lever, Welcome to the Jungle, DigitalRecruiters, Flatchr, Cegid Talentsoft, Ashby, Avature, Taleez.

If the vendor published API docs, the guide links them. If they published MCP, that link is shown too.

---

## 9. Test locally

```bash
composer install
./vendor/bin/phpunit
```

Mock the PSR HTTP client. Do not hit a live ATS in unit tests.

---

## 10. Support

In-app: **Settings → Support**  
Docs: [docs/SDK.md](docs/SDK.md) · [OpenPro MCP](https://github.com/RemiPelloux/openpro-backend)  
Product: [openpro.ai](https://openpro.ai)
