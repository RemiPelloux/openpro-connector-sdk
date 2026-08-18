# OpenPro Connector SDK

Libreria ufficiale per sincronizzare le offerte di qualsiasi ATS su [OpenPro](https://openpro.ai).

**Repository:** https://github.com/RemiPelloux/openpro-connector-sdk

**Docs:** [English](../../README.md) · [Français](README.fr.md) · [Español](README.es.md) · [Deutsch](README.de.md) · [Italiano](README.it.md) · [Português](README.pt.md) · [Polski](README.pl.md) · [Nederlands](README.nl.md)

I recruiter creano chiavi API e aprono le guide ATS in **Impostazioni → Connessione ATS**:

https://openpro.ai/settings?section=ats-connexion

---

## 1. Cosa fa questo SDK

OpenPro è la destinazione. Il tuo ATS resta la fonte di verità delle posizioni.

```
API ATS  →  Connettore (questo SDK)  →  OpenPro  →  offerte, pipeline, Mia, MCP
```

- Chiama l’API REST da **qualsiasi linguaggio** (token Bearer).
- Usa il pacchetto PHP per mappare le offerte su `NormalizedOffer` e pubblicarle.
- Registra un connettore in OpenPro per installarlo da **Impostazioni → Connettori**.

| Superficie | URL |
|---|---|
| Prodotto | https://openpro.ai |
| API REST | https://api.openpro.ai/api |
| MCP | https://mcp.openpro.ai |
| Consenso OAuth | https://openpro.ai/oauth/mcp/consent |
| Connessione ATS | https://openpro.ai/settings?section=ats-connexion |

Ogni richiesta può inviare `language` (`it`, `en`, `fr`, …). L’SDK imposta anche `Accept-Language` e `X-Language`.

---

## 2. Creare una chiave API

1. Accedi come recruiter su [openpro.ai](https://openpro.ai).
2. Apri **Impostazioni → Connessione ATS → Chiavi API**.
3. Dai un nome alla chiave (es. `Greenhouse` o `Cursor`).
4. Copia subito il segreto. OpenPro memorizza solo un hash.

Limite: 10 chiavi per account. Revoca quelle inutilizzate nello stesso modal.

Non committare mai la chiave. Usa `OPENPRO_API_TOKEN`.

| Metodo | Percorso | Uso |
|---|---|---|
| `GET` | `/developer/tokens` | Elencare (senza secret) |
| `POST` | `/developer/tokens` | Creare |
| `DELETE` | `/developer/tokens/{id}` | Revocare |
| `POST` | `/job_posts` | Pubblicare un’offerta mappata |

---

## 3. Tutti i linguaggi

Il contratto è HTTP. Esempi pronti: [examples/](../../examples/) e il [README inglese](../../README.md#3-use-from-any-language) (TypeScript, Python, Go, Java, C#, Ruby, Kotlin).

```http
Authorization: Bearer <api_key>
Accept: application/json
X-Language: it
```

---

## 4. Installare l’SDK PHP

PHP 8.2+ e un client HTTP PSR-18 (va bene Guzzle).

```bash
composer config repositories.openpro vcs https://github.com/RemiPelloux/openpro-connector-sdk
composer require openpro/connector-sdk
```

---

## 5. Pubblicare un’offerta

```php
$client = new OpenProClient(
    new Client(),
    $factory,
    $factory,
    new OpenProClientOptions(token: getenv('OPENPRO_API_TOKEN'), language: 'it'),
);

$offer = NormalizedOffer::fromArray([
    'external_id' => 'gh-12',
    'source_url' => 'https://boards.greenhouse.io/jobs/12',
    'title' => 'Ingegnere backend',
    'content' => 'Pubblicare l’API.',
    'location' => 'Milano',
]);

$client->publishJob($offer, draft: true);
```

---

## 6. Creare un connettore

Estendi `AbstractScraperConnector`, dichiara il `manifest()` e fai `yield NormalizedOffer::fromArray(...)` in `fetchOffers()`.

Modello: [`examples/ExampleConnector.php`](../../examples/ExampleConnector.php). Campi: [docs/SDK.md](../SDK.md).

---

## 7. NormalizedOffer

Obbligatori: `external_id`, `source_url`, `title`, `content`, `location`.

`toJobPostPayload()` rispecchia `POST /job_posts`. Flag: `draft_only`, `generate_video`, `generate_ai_image`, `auto_close_missing`.

---

## 8. Connettere MCP

Aggiungi `https://mcp.openpro.ai` in Cursor o Claude Desktop e completa OAuth.

La stessa chiave API funziona come `OPENPRO_MCP_TOKEN` in stdio. OAuth remoto è il percorso consigliato.

---

## 9. ATS in Europa

Workday, SAP SuccessFactors, Oracle Recruiting, Greenhouse, SmartRecruiters, Teamtailor, Personio, softgarden, Recruitee, Workable, Bullhorn, iCIMS, Lever, Welcome to the Jungle, DigitalRecruiters, Flatchr, Cegid Talentsoft, Ashby, Avature, Taleez.

---

## 10. Test

```bash
composer install
./vendor/bin/phpunit
```

Mocka il client HTTP PSR. Non chiamare un ATS né l’API OpenPro nei test unitari.

---

## 11. Supporto

In-app: **Impostazioni → Supporto** su [openpro.ai](https://openpro.ai)  
Issues: https://github.com/RemiPelloux/openpro-connector-sdk/issues
