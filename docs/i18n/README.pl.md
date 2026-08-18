# OpenPro Connector SDK

Oficjalna biblioteka do synchronizacji ofert z dowolnego ATS do [OpenPro](https://openpro.ai).

**Repozytorium:** https://github.com/RemiPelloux/openpro-connector-sdk

**Docs:** [English](../../README.md) · [Français](README.fr.md) · [Español](README.es.md) · [Deutsch](README.de.md) · [Italiano](README.it.md) · [Português](README.pt.md) · [Polski](README.pl.md) · [Nederlands](README.nl.md)

Rekruterzy tworzą klucze API i otwierają przewodniki ATS w **Ustawienia → Połączenie ATS**:

https://openpro.ai/settings?section=ats-connexion

---

## 1. Co robi ten SDK

OpenPro jest miejscem docelowym. ATS pozostaje źródłem prawdy o wakatach.

```
API ATS  →  Connector (ten SDK)  →  OpenPro  →  oferty, pipeline, Mia, MCP
```

- Wywołuj REST API z **dowolnego języka** (token Bearer).
- Użyj pakietu PHP, aby mapować oferty na `NormalizedOffer` i je publikować.
- Zarejestruj konektor w OpenPro, aby zainstalować go w **Ustawienia → Konektory**.

| Powierzchnia | URL |
|---|---|
| Produkt | https://openpro.ai |
| REST API | https://api.openpro.ai/api |
| MCP | https://mcp.openpro.ai |
| Zgoda OAuth | https://openpro.ai/oauth/mcp/consent |
| Połączenie ATS | https://openpro.ai/settings?section=ats-connexion |

Każde żądanie może wysłać `language` (`pl`, `en`, `fr`, …). SDK ustawia też `Accept-Language` i `X-Language`.

---

## 2. Utwórz klucz API

1. Zaloguj się jako rekruter na [openpro.ai](https://openpro.ai).
2. Otwórz **Ustawienia → Połączenie ATS → Klucze API**.
3. Nazwij klucz (np. `Greenhouse` lub `Cursor`).
4. Skopiuj sekret od razu. OpenPro zapisuje tylko hash.

Limit: 10 kluczy na konto. Nieużywane klucze unieważnij w tym samym modalu.

Nie commituj klucza. Użyj `OPENPRO_API_TOKEN`.

| Metoda | Ścieżka | Cel |
|---|---|---|
| `GET` | `/developer/tokens` | Lista (bez sekretów) |
| `POST` | `/developer/tokens` | Utwórz |
| `DELETE` | `/developer/tokens/{id}` | Unieważnij |
| `POST` | `/job_posts` | Opublikuj zmapowaną ofertę |

---

## 3. Wszystkie języki programowania

Kontrakt to HTTP. Gotowe przykłady: [examples/](../../examples/) oraz [angielski README](../../README.md#3-use-from-any-language) (TypeScript, Python, Go, Java, C#, Ruby, Kotlin).

```http
Authorization: Bearer <api_key>
Accept: application/json
X-Language: pl
```

---

## 4. Instalacja SDK PHP

PHP 8.2+ i klient HTTP PSR-18 (Guzzle wystarczy).

```bash
composer config repositories.openpro vcs https://github.com/RemiPelloux/openpro-connector-sdk
composer require openpro/connector-sdk
```

---

## 5. Publikacja oferty

```php
$client = new OpenProClient(
    new Client(),
    $factory,
    $factory,
    new OpenProClientOptions(token: getenv('OPENPRO_API_TOKEN'), language: 'pl'),
);

$offer = NormalizedOffer::fromArray([
    'external_id' => 'gh-12',
    'source_url' => 'https://boards.greenhouse.io/jobs/12',
    'title' => 'Inżynier backend',
    'content' => 'Wdróż API.',
    'location' => 'Warszawa',
]);

$client->publishJob($offer, draft: true);
```

---

## 6. Zbuduj konektor

Rozszerz `AbstractScraperConnector`, zdefiniuj `manifest()`, a w `fetchOffers()` zwracaj `NormalizedOffer::fromArray(...)`.

Szablon: [`examples/ExampleConnector.php`](../../examples/ExampleConnector.php). Pola: [docs/SDK.md](../SDK.md).

---

## 7. NormalizedOffer

Wymagane: `external_id`, `source_url`, `title`, `content`, `location`.

`toJobPostPayload()` odpowiada `POST /job_posts`. Flagi: `draft_only`, `generate_video`, `generate_ai_image`, `auto_close_missing`.

---

## 8. Połącz MCP

Dodaj `https://mcp.openpro.ai` w Cursor lub Claude Desktop i dokończ OAuth.

Ten sam klucz API działa jako `OPENPRO_MCP_TOKEN` w stdio. Zdalny OAuth jest zalecany.

---

## 9. ATS w Europie

Workday, SAP SuccessFactors, Oracle Recruiting, Greenhouse, SmartRecruiters, Teamtailor, Personio, softgarden, Recruitee, Workable, Bullhorn, iCIMS, Lever, Welcome to the Jungle, DigitalRecruiters, Flatchr, Cegid Talentsoft, Ashby, Avature, Taleez.

---

## 10. Testy

```bash
composer install
./vendor/bin/phpunit
```

Mockuj klienta HTTP PSR. Nie wywołuj ATS ani API OpenPro w testach jednostkowych.

---

## 11. Wsparcie

W aplikacji: **Ustawienia → Wsparcie** na [openpro.ai](https://openpro.ai)  
Issues: https://github.com/RemiPelloux/openpro-connector-sdk/issues
