# OpenPro Connector SDK

Biblioteca oficial para sincronizar ofertas de qualquer ATS para o [OpenPro](https://openpro.ai).

**Repositório:** https://github.com/RemiPelloux/openpro-connector-sdk

**Docs:** [English](../../README.md) · [Français](README.fr.md) · [Español](README.es.md) · [Deutsch](README.de.md) · [Italiano](README.it.md) · [Português](README.pt.md) · [Polski](README.pl.md) · [Nederlands](README.nl.md)

Os recrutadores criam chaves API e abrem os guias ATS em **Definições → Ligação ATS**:

https://openpro.ai/settings?section=ats-connexion

---

## 1. O que este SDK faz

O OpenPro é o destino. O seu ATS continua a ser a fonte de verdade das vagas.

```
API ATS  →  Conector (este SDK)  →  OpenPro  →  ofertas, pipeline, Mia, MCP
```

- Chame a API REST a partir de **qualquer linguagem** (token Bearer).
- Use o pacote PHP para mapear ofertas para `NormalizedOffer` e publicá-las.
- Registe um conector no OpenPro para o instalar em **Definições → Conectores**.

| Superfície | URL |
|---|---|
| Produto | https://openpro.ai |
| API REST | https://api.openpro.ai/api |
| MCP | https://mcp.openpro.ai |
| Consentimento OAuth | https://openpro.ai/oauth/mcp/consent |
| Ligação ATS | https://openpro.ai/settings?section=ats-connexion |

Cada pedido pode enviar `language` (`pt`, `en`, `fr`, …). O SDK também envia `Accept-Language` e `X-Language`.

---

## 2. Criar uma chave API

1. Entre como recrutador em [openpro.ai](https://openpro.ai).
2. Abra **Definições → Ligação ATS → Chaves API**.
3. Dê um nome à chave (ex. `Greenhouse` ou `Cursor`).
4. Copie o segredo imediatamente. O OpenPro guarda apenas um hash.

Limite: 10 chaves por conta. Revogue as que não usar no mesmo modal.

Nunca faça commit da chave. Use `OPENPRO_API_TOKEN`.

| Método | Caminho | Uso |
|---|---|---|
| `GET` | `/developer/tokens` | Listar (sem segredos) |
| `POST` | `/developer/tokens` | Criar |
| `DELETE` | `/developer/tokens/{id}` | Revogar |
| `POST` | `/job_posts` | Publicar uma oferta mapeada |

---

## 3. Todas as linguagens

O contrato é HTTP. Exemplos prontos: [examples/](../../examples/) e o [README em inglês](../../README.md#3-use-from-any-language) (TypeScript, Python, Go, Java, C#, Ruby, Kotlin).

```http
Authorization: Bearer <api_key>
Accept: application/json
X-Language: pt
```

---

## 4. Instalar o SDK PHP

PHP 8.2+ e um cliente HTTP PSR-18 (o Guzzle serve).

```bash
composer config repositories.openpro vcs https://github.com/RemiPelloux/openpro-connector-sdk
composer require openpro/connector-sdk
```

---

## 5. Publicar uma oferta

```php
$client = new OpenProClient(
    new Client(),
    $factory,
    $factory,
    new OpenProClientOptions(token: getenv('OPENPRO_API_TOKEN'), language: 'pt'),
);

$offer = NormalizedOffer::fromArray([
    'external_id' => 'gh-12',
    'source_url' => 'https://boards.greenhouse.io/jobs/12',
    'title' => 'Engenheiro backend',
    'content' => 'Publicar a API.',
    'location' => 'Lisboa',
]);

$client->publishJob($offer, draft: true);
```

---

## 6. Criar um conector

Estenda `AbstractScraperConnector`, declare o `manifest()` e faça `yield NormalizedOffer::fromArray(...)` em `fetchOffers()`.

Modelo: [`examples/ExampleConnector.php`](../../examples/ExampleConnector.php). Campos: [docs/SDK.md](../SDK.md).

---

## 7. NormalizedOffer

Obrigatórios: `external_id`, `source_url`, `title`, `content`, `location`.

`toJobPostPayload()` espelha `POST /job_posts`. Flags: `draft_only`, `generate_video`, `generate_ai_image`, `auto_close_missing`.

---

## 8. Ligar o MCP

Adicione `https://mcp.openpro.ai` no Cursor ou no Claude Desktop e conclua o OAuth.

A mesma chave API funciona como `OPENPRO_MCP_TOKEN` em stdio. O OAuth remoto é o caminho recomendado.

---

## 9. ATS na Europa

Workday, SAP SuccessFactors, Oracle Recruiting, Greenhouse, SmartRecruiters, Teamtailor, Personio, softgarden, Recruitee, Workable, Bullhorn, iCIMS, Lever, Welcome to the Jungle, DigitalRecruiters, Flatchr, Cegid Talentsoft, Ashby, Avature, Taleez.

---

## 10. Testes

```bash
composer install
./vendor/bin/phpunit
```

Faça mock do cliente HTTP PSR. Não chame um ATS nem a API OpenPro nos testes unitários.

---

## 11. Suporte

Na app: **Definições → Suporte** em [openpro.ai](https://openpro.ai)  
Issues: https://github.com/RemiPelloux/openpro-connector-sdk/issues
