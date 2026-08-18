# OpenPro Connector SDK

Offizielle Bibliothek, um Stellen aus jedem ATS nach [OpenPro](https://openpro.ai) zu synchronisieren.

**Repository:** https://github.com/RemiPelloux/openpro-connector-sdk

**Docs:** [English](../../README.md) · [Français](README.fr.md) · [Español](README.es.md) · [Deutsch](README.de.md) · [Italiano](README.it.md) · [Português](README.pt.md) · [Polski](README.pl.md) · [Nederlands](README.nl.md)

Recruiter erstellen API-Schlüssel und öffnen die ATS-Anleitungen unter **Einstellungen → ATS-Verbindung**:

https://openpro.ai/settings?section=ats-connexion

---

## 1. Was dieses SDK leistet

OpenPro ist das Ziel. Ihr ATS bleibt die Quelle der Wahrheit für Stellen.

```
ATS-API  →  Connector (dieses SDK)  →  OpenPro  →  Jobs, Pipeline, Mia, MCP
```

- Rufen Sie die REST-API aus **jeder Sprache** auf (Bearer-Token).
- Nutzen Sie das PHP-Paket, um Jobs auf `NormalizedOffer` zu mappen und zu veröffentlichen.
- Registrieren Sie einen Connector in OpenPro zur Installation unter **Einstellungen → Konnektoren**.

| Fläche | URL |
|---|---|
| Produkt | https://openpro.ai |
| REST-API | https://api.openpro.ai/api |
| MCP | https://mcp.openpro.ai |
| OAuth-Consent | https://openpro.ai/oauth/mcp/consent |
| ATS-Verbindung | https://openpro.ai/settings?section=ats-connexion |

Jeder Request kann `language` senden (`de`, `en`, `fr`, …). Das SDK setzt zusätzlich `Accept-Language` und `X-Language`.

---

## 2. API-Schlüssel erstellen

1. Als Recruiter auf [openpro.ai](https://openpro.ai) anmelden.
2. **Einstellungen → ATS-Verbindung → API-Schlüssel** öffnen.
3. Den Schlüssel benennen (z. B. `Greenhouse` oder `Cursor`).
4. Das Geheimnis sofort kopieren. OpenPro speichert nur einen Hash.

Limit: 10 Schlüssel pro Konto. Ungenutzte Schlüssel im selben Modal widerrufen.

Den Schlüssel nie committen. `OPENPRO_API_TOKEN` verwenden.

| Methode | Pfad | Zweck |
|---|---|---|
| `GET` | `/developer/tokens` | Auflisten (ohne Secrets) |
| `POST` | `/developer/tokens` | Anlegen |
| `DELETE` | `/developer/tokens/{id}` | Widerrufen |
| `POST` | `/job_posts` | Gemapptes Angebot veröffentlichen |

---

## 3. Alle Sprachen

Der Vertrag ist HTTP. Fertige Beispiele: [examples/](../../examples/) und das [englische README](../../README.md#3-use-from-any-language) (TypeScript, Python, Go, Java, C#, Ruby, Kotlin).

```http
Authorization: Bearer <api_key>
Accept: application/json
X-Language: de
```

---

## 4. PHP-SDK installieren

PHP 8.2+ und ein PSR-18-HTTP-Client (Guzzle eignet sich).

```bash
composer config repositories.openpro vcs https://github.com/RemiPelloux/openpro-connector-sdk
composer require openpro/connector-sdk
```

---

## 5. Stelle veröffentlichen

```php
$client = new OpenProClient(
    new Client(),
    $factory,
    $factory,
    new OpenProClientOptions(token: getenv('OPENPRO_API_TOKEN'), language: 'de'),
);

$offer = NormalizedOffer::fromArray([
    'external_id' => 'gh-12',
    'source_url' => 'https://boards.greenhouse.io/jobs/12',
    'title' => 'Backend-Ingenieur',
    'content' => 'Die API liefern.',
    'location' => 'Berlin',
]);

$client->publishJob($offer, draft: true);
```

---

## 6. Connector bauen

`AbstractScraperConnector` erweitern, `manifest()` definieren, in `fetchOffers()` `NormalizedOffer::fromArray(...)` yielden.

Vorlage: [`examples/ExampleConnector.php`](../../examples/ExampleConnector.php). Felder: [docs/SDK.md](../SDK.md).

---

## 7. NormalizedOffer

Pflicht: `external_id`, `source_url`, `title`, `content`, `location`.

`toJobPostPayload()` entspricht `POST /job_posts`. Flags: `draft_only`, `generate_video`, `generate_ai_image`, `auto_close_missing`.

---

## 8. MCP verbinden

`https://mcp.openpro.ai` in Cursor oder Claude Desktop hinzufügen und OAuth abschließen.

Derselbe API-Schlüssel funktioniert als `OPENPRO_MCP_TOKEN` für stdio. Remote-OAuth ist der empfohlene Weg.

---

## 9. ATS in Europa

Workday, SAP SuccessFactors, Oracle Recruiting, Greenhouse, SmartRecruiters, Teamtailor, Personio, softgarden, Recruitee, Workable, Bullhorn, iCIMS, Lever, Welcome to the Jungle, DigitalRecruiters, Flatchr, Cegid Talentsoft, Ashby, Avature, Taleez.

---

## 10. Tests

```bash
composer install
./vendor/bin/phpunit
```

PSR-HTTP-Client mocken. Kein Live-ATS und keine OpenPro-API in Unit-Tests.

---

## 11. Support

In der App: **Einstellungen → Support** auf [openpro.ai](https://openpro.ai)  
Issues: https://github.com/RemiPelloux/openpro-connector-sdk/issues
