# OpenPro Connector SDK

Official library to sync jobs from any ATS into [OpenPro](https://openpro.ai).

[![CI](https://github.com/RemiPelloux/openpro-connector-sdk/actions/workflows/ci.yml/badge.svg)](https://github.com/RemiPelloux/openpro-connector-sdk/actions/workflows/ci.yml)

**Repository:** https://github.com/RemiPelloux/openpro-connector-sdk

**Docs:** [English](README.md) · [Français](docs/i18n/README.fr.md) · [Español](docs/i18n/README.es.md) · [Deutsch](docs/i18n/README.de.md) · [Italiano](docs/i18n/README.it.md) · [Português](docs/i18n/README.pt.md) · [Polski](docs/i18n/README.pl.md) · [Nederlands](docs/i18n/README.nl.md)

Recruiters create API keys and open per-ATS guides in **Settings → ATS connection**:

https://openpro.ai/settings?section=ats-connexion

---

## Wiki

1. [What this SDK does](#1-what-this-sdk-does)
2. [Create an API key](#2-create-an-api-key)
3. [Use from any language](#3-use-from-any-language)
4. [Install the PHP SDK](#4-install-the-php-sdk)
5. [Publish a job](#5-publish-a-job)
6. [Build a connector](#6-build-a-connector)
7. [NormalizedOffer](#7-normalizedoffer)
8. [Connect MCP](#8-connect-mcp)
9. [ATS in Europe](#9-ats-in-europe)
10. [Run the tests](#10-run-the-tests)
11. [Support](#11-support)

---

## 1. What this SDK does

OpenPro is the destination. Your ATS stays the source of truth for requisitions.

```
ATS API  →  Connector (this SDK)  →  OpenPro  →  jobs, pipeline, Mia, MCP
```

You can:

- Call the REST API from **any language** (Bearer token).
- Use the PHP package to map ATS jobs to `NormalizedOffer` and publish them.
- Register a connector inside OpenPro so recruiters install it from **Settings → Connectors**.

| Surface | URL |
|---|---|
| Product | https://openpro.ai |
| REST API | https://api.openpro.ai/api |
| MCP | https://mcp.openpro.ai |
| OAuth consent | https://openpro.ai/oauth/mcp/consent |
| ATS connection | https://openpro.ai/settings?section=ats-connexion |

Every request can send `language` (`en`, `fr`, `es`, `de`, `it`, `pt`, `pl`, `nl`, …). The SDK also sets `Accept-Language` and `X-Language`.

---

## 2. Create an API key

1. Sign in as a recruiter on [openpro.ai](https://openpro.ai).
2. Open **Settings → ATS connection → API keys**.
3. Name the key (for example `Greenhouse` or `Cursor`).
4. Copy the secret immediately. OpenPro stores only a hash.

Limits: 10 keys per account. Revoke unused keys from the same modal.

Never commit the key. Use `OPENPRO_API_TOKEN`.

```http
Authorization: Bearer <api_key>
Accept: application/json
X-Language: en
```

| Method | Path | Purpose |
|---|---|---|
| `GET` | `/developer/tokens` | List keys (no secrets) |
| `POST` | `/developer/tokens` | Create a key |
| `DELETE` | `/developer/tokens/{id}` | Revoke |
| `POST` | `/job_posts` | Publish a mapped offer |

---

## 3. Use from any language

The contract is HTTP. Copy a snippet from [`examples/`](examples/) or below.

<details>
<summary>TypeScript</summary>

```ts
const token = process.env.OPENPRO_API_TOKEN!;
const offer = {
  title: 'Backend engineer',
  content: 'Ship the API.',
  location: 'Paris',
  status: 'draft',
  source_url: 'https://boards.greenhouse.io/jobs/12',
  language: 'en',
};

await fetch('https://api.openpro.ai/api/job_posts', {
  method: 'POST',
  headers: {
    Authorization: `Bearer ${token}`,
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-Language': 'en',
  },
  body: JSON.stringify(offer),
});
```

</details>

<details>
<summary>Python</summary>

```python
import os, requests

requests.post(
    "https://api.openpro.ai/api/job_posts",
    headers={
        "Authorization": f"Bearer {os.environ['OPENPRO_API_TOKEN']}",
        "Accept": "application/json",
        "X-Language": "en",
    },
    json={
        "title": "Backend engineer",
        "content": "Ship the API.",
        "location": "Paris",
        "status": "draft",
        "source_url": "https://boards.greenhouse.io/jobs/12",
        "language": "en",
    },
)
```

</details>

<details>
<summary>Go</summary>

```go
req, _ := http.NewRequest(http.MethodPost, "https://api.openpro.ai/api/job_posts?language=en", bytes.NewBuffer(payload))
req.Header.Set("Authorization", "Bearer "+os.Getenv("OPENPRO_API_TOKEN"))
req.Header.Set("Accept", "application/json")
req.Header.Set("Content-Type", "application/json")
req.Header.Set("X-Language", "en")
http.DefaultClient.Do(req)
```

</details>

<details>
<summary>Java</summary>

```java
HttpRequest request = HttpRequest.newBuilder()
    .uri(URI.create("https://api.openpro.ai/api/job_posts?language=en"))
    .header("Authorization", "Bearer " + System.getenv("OPENPRO_API_TOKEN"))
    .header("Accept", "application/json")
    .header("Content-Type", "application/json")
    .header("X-Language", "en")
    .POST(HttpRequest.BodyPublishers.ofString(json))
    .build();
HttpClient.newHttpClient().send(request, HttpResponse.BodyHandlers.ofString());
```

</details>

<details>
<summary>C#</summary>

```csharp
using var client = new HttpClient();
client.DefaultRequestHeaders.Authorization = new AuthenticationHeaderValue("Bearer", token);
client.DefaultRequestHeaders.Add("X-Language", "en");
await client.PostAsync("https://api.openpro.ai/api/job_posts?language=en",
    new StringContent(json, Encoding.UTF8, "application/json"));
```

</details>

<details>
<summary>Ruby</summary>

```ruby
require "net/http"
require "json"

uri = URI("https://api.openpro.ai/api/job_posts?language=en")
req = Net::HTTP::Post.new(uri)
req["Authorization"] = "Bearer #{ENV.fetch("OPENPRO_API_TOKEN")}"
req["X-Language"] = "en"
req["Content-Type"] = "application/json"
req.body = JSON.dump(offer)
Net::HTTP.start(uri.host, uri.port, use_ssl: true) { |http| http.request(req) }
```

</details>

<details>
<summary>Kotlin</summary>

```kotlin
val client = OkHttpClient()
val body = json.toRequestBody("application/json".toMediaType())
val request = Request.Builder()
    .url("https://api.openpro.ai/api/job_posts?language=en")
    .addHeader("Authorization", "Bearer $token")
    .addHeader("X-Language", "en")
    .post(body)
    .build()
client.newCall(request).execute()
```

</details>

Ready-to-copy files: [`examples/typescript`](examples/typescript/publish.ts) · [`examples/python`](examples/python/publish.py) · [`examples/go`](examples/go/publish.go) · [`examples/java`](examples/java/Publish.java) · [`examples/csharp`](examples/csharp/Publish.cs) · [`examples/ruby`](examples/ruby/publish.rb) · [`examples/kotlin`](examples/kotlin/Publish.kt)

---

## 4. Install the PHP SDK

PHP 8.2+ and a PSR-18 HTTP client (Guzzle works).

```bash
composer config repositories.openpro vcs https://github.com/RemiPelloux/openpro-connector-sdk
composer require openpro/connector-sdk
```

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/RemiPelloux/openpro-connector-sdk"
    }
  ],
  "require": {
    "openpro/connector-sdk": "^1.0",
    "guzzlehttp/guzzle": "^7.0"
  }
}
```

---

## 5. Publish a job

```php
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use OpenPro\ConnectorSdk\Client\OpenProClient;
use OpenPro\ConnectorSdk\Client\OpenProClientOptions;
use OpenPro\ConnectorSdk\Offer\NormalizedOffer;

$factory = new HttpFactory();
$client = new OpenProClient(
    new Client(),
    $factory,
    $factory,
    new OpenProClientOptions(
        token: getenv('OPENPRO_API_TOKEN'),
        language: 'fr',
    ),
);

$offer = NormalizedOffer::fromArray([
    'external_id' => 'gh-12',
    'source_url' => 'https://boards.greenhouse.io/jobs/12',
    'title' => 'Backend engineer',
    'content' => 'Ship the API.',
    'location' => 'Paris',
    'contract_type' => 'CDI',
    'currency' => 'EUR',
]);

$client->publishJob($offer, draft: true);
```

The client sends `language`, `Accept-Language`, and `X-Language` on every call.

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
        // call the ATS API, yield NormalizedOffer::fromArray(...)
    }
}
```

Starter: [`examples/ExampleConnector.php`](examples/ExampleConnector.php). Field notes: [docs/SDK.md](docs/SDK.md).

---

## 7. NormalizedOffer

Required: `external_id`, `source_url`, `title`, `content`, `location`.

Also mapped: coordinates, contract, remuneration, schedule, missions, skills, advantages, `metier`, `source`.

`toJobPostPayload()` mirrors `POST /job_posts`. Installation flags:

- `draft_only`
- `generate_video`
- `generate_ai_image`
- `auto_close_missing`

---

## 8. Connect MCP

Add `https://mcp.openpro.ai` in Cursor or Claude Desktop and complete OAuth.

The same API key works as `OPENPRO_MCP_TOKEN` for stdio clients. Remote OAuth is the recommended path.

Most ATS vendors do not ship an official MCP server. Use OpenPro MCP plus this SDK.

---

## 9. ATS in Europe

In-product guides (logo + MCP / API / SDK steps):

Workday, SAP SuccessFactors, Oracle Recruiting, Greenhouse, SmartRecruiters, Teamtailor, Personio, softgarden, Recruitee, Workable, Bullhorn, iCIMS, Lever, Welcome to the Jungle, DigitalRecruiters, Flatchr, Cegid Talentsoft, Ashby, Avature, Taleez.

---

## 10. Run the tests

```bash
composer install
./vendor/bin/phpunit
```

Mock the PSR HTTP client. Do not hit a live ATS or the OpenPro API in unit tests.

---

## 11. Support

In-app: **Settings → Support** on [openpro.ai](https://openpro.ai)  
Docs: [docs/SDK.md](docs/SDK.md)  
Issues: https://github.com/RemiPelloux/openpro-connector-sdk/issues
