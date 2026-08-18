# OpenPro Connector SDK

Bibliothèque officielle pour synchroniser les offres de n’importe quel ATS vers [OpenPro](https://openpro.ai).

**Dépôt :** https://github.com/RemiPelloux/openpro-connector-sdk

**Docs :** [English](../../README.md) · [Français](README.fr.md) · [Español](README.es.md) · [Deutsch](README.de.md) · [Italiano](README.it.md) · [Português](README.pt.md) · [Polski](README.pl.md) · [Nederlands](README.nl.md)

Les recruteurs créent des clés API et ouvrent les guides ATS dans **Paramètres → Connexion ATS** :

https://openpro.ai/settings?section=ats-connexion

---

## 1. Rôle du SDK

OpenPro est la destination. Votre ATS reste la source de vérité des postes.

```
API ATS  →  Connecteur (ce SDK)  →  OpenPro  →  offres, pipeline, Mia, MCP
```

- Appelez l’API REST depuis **n’importe quel langage** (jeton Bearer).
- Utilisez le package PHP pour mapper les offres vers `NormalizedOffer` et les publier.
- Enregistrez un connecteur dans OpenPro pour l’installer depuis **Paramètres → Connecteurs**.

| Surface | URL |
|---|---|
| Produit | https://openpro.ai |
| API REST | https://api.openpro.ai/api |
| MCP | https://mcp.openpro.ai |
| Consentement OAuth | https://openpro.ai/oauth/mcp/consent |
| Connexion ATS | https://openpro.ai/settings?section=ats-connexion |

Chaque requête peut envoyer `language` (`fr`, `en`, `es`, …). Le SDK envoie aussi `Accept-Language` et `X-Language`.

---

## 2. Créer une clé API

1. Connectez-vous en recruteur sur [openpro.ai](https://openpro.ai).
2. Ouvrez **Paramètres → Connexion ATS → Clés API**.
3. Nommez la clé (ex. `Greenhouse` ou `Cursor`).
4. Copiez le secret immédiatement. OpenPro ne stocke qu’un hash.

Limite : 10 clés par compte. Révoquez les clés inutilisées dans le même modal.

Ne commitez jamais la clé. Utilisez `OPENPRO_API_TOKEN`.

| Méthode | Chemin | Rôle |
|---|---|---|
| `GET` | `/developer/tokens` | Lister (sans secrets) |
| `POST` | `/developer/tokens` | Créer |
| `DELETE` | `/developer/tokens/{id}` | Révoquer |
| `POST` | `/job_posts` | Publier une offre mappée |

---

## 3. Tous les langages

Le contrat est HTTP. Exemples prêts à copier : [examples/](../../examples/) et le [README anglais](../../README.md#3-use-from-any-language) (TypeScript, Python, Go, Java, C#, Ruby, Kotlin).

```http
Authorization: Bearer <api_key>
Accept: application/json
X-Language: fr
```

---

## 4. Installer le SDK PHP

PHP 8.2+ et un client HTTP PSR-18 (Guzzle convient).

```bash
composer config repositories.openpro vcs https://github.com/RemiPelloux/openpro-connector-sdk
composer require openpro/connector-sdk
```

---

## 5. Publier une offre

```php
$client = new OpenProClient(
    new Client(),
    $factory,
    $factory,
    new OpenProClientOptions(token: getenv('OPENPRO_API_TOKEN'), language: 'fr'),
);

$offer = NormalizedOffer::fromArray([
    'external_id' => 'gh-12',
    'source_url' => 'https://boards.greenhouse.io/jobs/12',
    'title' => 'Ingénieur backend',
    'content' => 'Livrer l’API.',
    'location' => 'Paris',
]);

$client->publishJob($offer, draft: true);
```

---

## 6. Créer un connecteur

Étendez `AbstractScraperConnector`, déclarez le `manifest()`, puis `yield NormalizedOffer::fromArray(...)` dans `fetchOffers()`.

Modèle : [`examples/ExampleConnector.php`](../../examples/ExampleConnector.php). Détail des champs : [docs/SDK.md](../SDK.md).

---

## 7. NormalizedOffer

Obligatoires : `external_id`, `source_url`, `title`, `content`, `location`.

`toJobPostPayload()` reflète `POST /job_posts`. Options d’installation : `draft_only`, `generate_video`, `generate_ai_image`, `auto_close_missing`.

---

## 8. Connecter MCP

Ajoutez `https://mcp.openpro.ai` dans Cursor ou Claude Desktop et terminez l’OAuth.

La même clé API fonctionne comme `OPENPRO_MCP_TOKEN` en stdio. L’OAuth distant est le chemin recommandé.

---

## 9. ATS en Europe

Workday, SAP SuccessFactors, Oracle Recruiting, Greenhouse, SmartRecruiters, Teamtailor, Personio, softgarden, Recruitee, Workable, Bullhorn, iCIMS, Lever, Welcome to the Jungle, DigitalRecruiters, Flatchr, Cegid Talentsoft, Ashby, Avature, Taleez.

---

## 10. Tests

```bash
composer install
./vendor/bin/phpunit
```

Mockez le client HTTP PSR. N’appelez pas un ATS ni l’API OpenPro dans les tests unitaires.

---

## 11. Support

Dans l’app : **Paramètres → Support** sur [openpro.ai](https://openpro.ai)  
Issues : https://github.com/RemiPelloux/openpro-connector-sdk/issues
