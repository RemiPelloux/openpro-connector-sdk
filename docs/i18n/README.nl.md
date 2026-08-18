# OpenPro Connector SDK

Officiële bibliotheek om vacatures uit elke ATS naar [OpenPro](https://openpro.ai) te synchroniseren.

**Repository:** https://github.com/RemiPelloux/openpro-connector-sdk

**Docs:** [English](../../README.md) · [Français](README.fr.md) · [Español](README.es.md) · [Deutsch](README.de.md) · [Italiano](README.it.md) · [Português](README.pt.md) · [Polski](README.pl.md) · [Nederlands](README.nl.md)

Recruiters maken API-sleutels en openen de ATS-gidsen in **Instellingen → ATS-verbinding**:

https://openpro.ai/settings?section=ats-connexion

---

## 1. Wat deze SDK doet

OpenPro is de bestemming. Jouw ATS blijft de bron van waarheid voor vacatures.

```
ATS-API  →  Connector (deze SDK)  →  OpenPro  →  vacatures, pipeline, Mia, MCP
```

- Roep de REST API aan vanuit **elke taal** (Bearer-token).
- Gebruik het PHP-pakket om vacatures te mappen naar `NormalizedOffer` en te publiceren.
- Registreer een connector in OpenPro om die te installeren via **Instellingen → Connectors**.

| Oppervlak | URL |
|---|---|
| Product | https://openpro.ai |
| REST API | https://api.openpro.ai/api |
| MCP | https://mcp.openpro.ai |
| OAuth-toestemming | https://openpro.ai/oauth/mcp/consent |
| ATS-verbinding | https://openpro.ai/settings?section=ats-connexion |

Elk request kan `language` meesturen (`nl`, `en`, `fr`, …). De SDK zet ook `Accept-Language` en `X-Language`.

---

## 2. Een API-sleutel aanmaken

1. Meld je aan als recruiter op [openpro.ai](https://openpro.ai).
2. Open **Instellingen → ATS-verbinding → API-sleutels**.
3. Geef de sleutel een naam (bijv. `Greenhouse` of `Cursor`).
4. Kopieer het geheim meteen. OpenPro bewaart alleen een hash.

Limiet: 10 sleutels per account. Trek ongebruikte sleutels in via hetzelfde modal.

Commit de sleutel nooit. Gebruik `OPENPRO_API_TOKEN`.

| Methode | Pad | Doel |
|---|---|---|
| `GET` | `/developer/tokens` | Lijst (geen secrets) |
| `POST` | `/developer/tokens` | Aanmaken |
| `DELETE` | `/developer/tokens/{id}` | Intrekken |
| `POST` | `/job_posts` | Gemapte vacature publiceren |

---

## 3. Alle programmeertalen

Het contract is HTTP. Kant-en-klare voorbeelden: [examples/](../../examples/) en de [Engelse README](../../README.md#3-use-from-any-language) (TypeScript, Python, Go, Java, C#, Ruby, Kotlin).

```http
Authorization: Bearer <api_key>
Accept: application/json
X-Language: nl
```

---

## 4. PHP-SDK installeren

PHP 8.2+ en een PSR-18 HTTP-client (Guzzle voldoet).

```bash
composer config repositories.openpro vcs https://github.com/RemiPelloux/openpro-connector-sdk
composer require openpro/connector-sdk
```

---

## 5. Een vacature publiceren

```php
$client = new OpenProClient(
    new Client(),
    $factory,
    $factory,
    new OpenProClientOptions(token: getenv('OPENPRO_API_TOKEN'), language: 'nl'),
);

$offer = NormalizedOffer::fromArray([
    'external_id' => 'gh-12',
    'source_url' => 'https://boards.greenhouse.io/jobs/12',
    'title' => 'Backend engineer',
    'content' => 'Publiceer de API.',
    'location' => 'Amsterdam',
]);

$client->publishJob($offer, draft: true);
```

---

## 6. Een connector bouwen

Breid `AbstractScraperConnector` uit, declareer `manifest()` en `yield NormalizedOffer::fromArray(...)` in `fetchOffers()`.

Sjabloon: [`examples/ExampleConnector.php`](../../examples/ExampleConnector.php). Velden: [docs/SDK.md](../SDK.md).

---

## 7. NormalizedOffer

Verplicht: `external_id`, `source_url`, `title`, `content`, `location`.

`toJobPostPayload()` volgt `POST /job_posts`. Flags: `draft_only`, `generate_video`, `generate_ai_image`, `auto_close_missing`.

---

## 8. MCP koppelen

Voeg `https://mcp.openpro.ai` toe in Cursor of Claude Desktop en rond OAuth af.

Dezelfde API-sleutel werkt als `OPENPRO_MCP_TOKEN` voor stdio. Remote OAuth is de aanbevolen weg.

---

## 9. ATS in Europa

Workday, SAP SuccessFactors, Oracle Recruiting, Greenhouse, SmartRecruiters, Teamtailor, Personio, softgarden, Recruitee, Workable, Bullhorn, iCIMS, Lever, Welcome to the Jungle, DigitalRecruiters, Flatchr, Cegid Talentsoft, Ashby, Avature, Taleez.

---

## 10. Tests

```bash
composer install
./vendor/bin/phpunit
```

Mock de PSR HTTP-client. Roep geen live ATS of de OpenPro API aan in unittests.

---

## 11. Support

In de app: **Instellingen → Support** op [openpro.ai](https://openpro.ai)  
Issues: https://github.com/RemiPelloux/openpro-connector-sdk/issues
