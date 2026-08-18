# OpenPro Connector SDK

Biblioteca oficial para sincronizar ofertas de cualquier ATS en [OpenPro](https://openpro.ai).

**Repositorio:** https://github.com/RemiPelloux/openpro-connector-sdk

**Docs:** [English](../../README.md) · [Français](README.fr.md) · [Español](README.es.md) · [Deutsch](README.de.md) · [Italiano](README.it.md) · [Português](README.pt.md) · [Polski](README.pl.md) · [Nederlands](README.nl.md)

Los reclutadores crean claves API y abren las guías ATS en **Ajustes → Conexión ATS**:

https://openpro.ai/settings?section=ats-connexion

---

## 1. Qué hace este SDK

OpenPro es el destino. Tu ATS sigue siendo la fuente de verdad de las vacantes.

```
API ATS  →  Conector (este SDK)  →  OpenPro  →  ofertas, pipeline, Mia, MCP
```

- Llama a la API REST desde **cualquier lenguaje** (token Bearer).
- Usa el paquete PHP para mapear ofertas a `NormalizedOffer` y publicarlas.
- Registra un conector en OpenPro para instalarlo desde **Ajustes → Conectores**.

| Superficie | URL |
|---|---|
| Producto | https://openpro.ai |
| API REST | https://api.openpro.ai/api |
| MCP | https://mcp.openpro.ai |
| Consentimiento OAuth | https://openpro.ai/oauth/mcp/consent |
| Conexión ATS | https://openpro.ai/settings?section=ats-connexion |

Cada petición puede enviar `language` (`es`, `en`, `fr`, …). El SDK también envía `Accept-Language` y `X-Language`.

---

## 2. Crear una clave API

1. Entra como reclutador en [openpro.ai](https://openpro.ai).
2. Abre **Ajustes → Conexión ATS → Claves API**.
3. Nombra la clave (p. ej. `Greenhouse` o `Cursor`).
4. Copia el secreto de inmediato. OpenPro solo guarda un hash.

Límite: 10 claves por cuenta. Revoca las que no uses en el mismo modal.

No subas la clave al repositorio. Usa `OPENPRO_API_TOKEN`.

| Método | Ruta | Uso |
|---|---|---|
| `GET` | `/developer/tokens` | Listar (sin secretos) |
| `POST` | `/developer/tokens` | Crear |
| `DELETE` | `/developer/tokens/{id}` | Revocar |
| `POST` | `/job_posts` | Publicar una oferta mapeada |

---

## 3. Todos los lenguajes

El contrato es HTTP. Ejemplos listos: [examples/](../../examples/) y el [README en inglés](../../README.md#3-use-from-any-language) (TypeScript, Python, Go, Java, C#, Ruby, Kotlin).

```http
Authorization: Bearer <api_key>
Accept: application/json
X-Language: es
```

---

## 4. Instalar el SDK PHP

PHP 8.2+ y un cliente HTTP PSR-18 (Guzzle vale).

```bash
composer config repositories.openpro vcs https://github.com/RemiPelloux/openpro-connector-sdk
composer require openpro/connector-sdk
```

---

## 5. Publicar una oferta

```php
$client = new OpenProClient(
    new Client(),
    $factory,
    $factory,
    new OpenProClientOptions(token: getenv('OPENPRO_API_TOKEN'), language: 'es'),
);

$offer = NormalizedOffer::fromArray([
    'external_id' => 'gh-12',
    'source_url' => 'https://boards.greenhouse.io/jobs/12',
    'title' => 'Ingeniero backend',
    'content' => 'Publicar la API.',
    'location' => 'Madrid',
]);

$client->publishJob($offer, draft: true);
```

---

## 6. Crear un conector

Extiende `AbstractScraperConnector`, declara el `manifest()` y haz `yield NormalizedOffer::fromArray(...)` en `fetchOffers()`.

Plantilla: [`examples/ExampleConnector.php`](../../examples/ExampleConnector.php). Campos: [docs/SDK.md](../SDK.md).

---

## 7. NormalizedOffer

Obligatorios: `external_id`, `source_url`, `title`, `content`, `location`.

`toJobPostPayload()` refleja `POST /job_posts`. Opciones: `draft_only`, `generate_video`, `generate_ai_image`, `auto_close_missing`.

---

## 8. Conectar MCP

Añade `https://mcp.openpro.ai` en Cursor o Claude Desktop y completa OAuth.

La misma clave API sirve como `OPENPRO_MCP_TOKEN` en stdio. OAuth remoto es el camino recomendado.

---

## 9. ATS en Europa

Workday, SAP SuccessFactors, Oracle Recruiting, Greenhouse, SmartRecruiters, Teamtailor, Personio, softgarden, Recruitee, Workable, Bullhorn, iCIMS, Lever, Welcome to the Jungle, DigitalRecruiters, Flatchr, Cegid Talentsoft, Ashby, Avature, Taleez.

---

## 10. Tests

```bash
composer install
./vendor/bin/phpunit
```

Simula el cliente HTTP PSR. No llames a un ATS ni a la API de OpenPro en los tests unitarios.

---

## 11. Soporte

En la app: **Ajustes → Soporte** en [openpro.ai](https://openpro.ai)  
Issues: https://github.com/RemiPelloux/openpro-connector-sdk/issues
