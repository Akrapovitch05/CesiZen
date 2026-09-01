# Plan de déploiement

## Architecture applicative

```
                    Navigateur (desktop / mobile)
                              │  HTTPS
                              ▼
                    ┌──────────────────┐
                    │  nginx 1.27      │  terminaison TLS, en-têtes de
                    │  (reverse proxy) │  sécurité, fichiers statiques
                    └────────┬─────────┘
                             │  FastCGI (réseau interne uniquement)
                             ▼
                    ┌──────────────────┐
                    │  PHP 8.2 FPM     │  Symfony 6.4
                    │  Symfony/Doctrine│  API Platform · EasyAdmin · Twig
                    └────────┬─────────┘
                             │  PDO (réseau interne uniquement)
                             ▼
                    ┌──────────────────┐
                    │  MySQL 8.0       │  volume persistant + sauvegardes
                    └──────────────────┘
```

Seul nginx expose un port. Ni PHP-FPM ni MySQL ne sont joignables depuis l'extérieur : la base n'a aucun port publié en production, sa seule voie d'accès est le réseau interne Docker.

**Ressources par environnement**

| Environnement | Processeur | Mémoire | Stockage |
|---|---|---|---|
| Développement | 2 vCPU | 4 Go | 20 Go |
| Test / CI | 2 vCPU | 4 Go | éphémère (tmpfs) |
| Préproduction | 2 vCPU | 4 Go | 40 Go |
| Production | 4 vCPU | 8 Go | 100 Go + sauvegardes |

Ce dimensionnement correspond à une plateforme grand public en phase de lancement. La conteneurisation permet de passer à une répartition de charge sur plusieurs instances PHP sans modifier l'application.

## Environnements

| | Développement | Test / CI | Préproduction | Production |
|---|---|---|---|---|
| **Finalité** | Écrire et déboguer | Valider automatiquement | Recetter avant livraison | Servir les utilisateurs |
| **Déclencheur** | Manuel | Chaque push et PR | Tag de version | Approbation manuelle |
| **`APP_ENV`** | `dev` | `test` | `prod` | `prod` |
| **Débogage** | Actif | Inactif | Inactif | Inactif |
| **Code** | Monté en volume | Dans l'image | Dans l'image | Dans l'image |
| **Base** | Volume local | tmpfs, jetable | Copie anonymisée de la production | Volume persistant + sauvegardes |
| **Données** | Fixtures | Fixtures | **Anonymisées** | Réelles |
| **Accès** | Développeur | CI | Équipe et Ministère | Public |
| **URL locale** | `localhost:8080` | — | `localhost:8081` | `localhost:8081` |

> **Point RGPD** : la préproduction ne reçoit **jamais** de données réelles. Les données de santé mentale sont sensibles ; les copier vers un environnement moins protégé, accessible à plus de personnes, constituerait une violation. La copie est anonymisée à l'export.

Commandes correspondantes (voir `cesizen.ps1` sous Windows, `Makefile` sous Linux/macOS) :

```bash
docker compose --env-file .env.local up -d                                                  # dev
docker compose --env-file .env.local -f compose.yaml -f compose.test.yaml -p cesizen-test up -d   # test
docker compose --env-file .env.local -f compose.yaml -f compose.prod.yaml -p cesizen-prod up -d   # prod
```

## Étapes de déploiement

### Développement
1. `.\cesizen.ps1 dev-up` — construction et démarrage
2. `.\cesizen.ps1 dev-migrate` — application des migrations
3. `.\cesizen.ps1 dev-fixtures` — jeu de données de démonstration

### Test — automatique, à chaque push
1. Récupération des sources
2. Contrôles statiques : syntaxe PHP, Twig, YAML, conteneur d'injection
3. Audit de sécurité des dépendances et recherche de secrets versionnés
4. Démarrage d'une base MySQL jetable, application des migrations
5. Exécution de la suite de tests
6. Construction de l'image de production pour vérifier que le livrable se construit

Un échec à n'importe quelle étape bloque la fusion.

### Préproduction — automatique, sur tag
1. Construction et publication de l'image versionnée
2. Sauvegarde de la base de préproduction
3. Application des migrations
4. Démarrage de la nouvelle version
5. Test de disponibilité
6. Recette fonctionnelle par le Ministère

### Production — après approbation manuelle
1. **Sauvegarde complète de la base, vérifiée.** Sans sauvegarde exploitable, le déploiement est interrompu.
2. Promotion de **l'image déjà validée en préproduction**. Aucune reconstruction : l'artefact livré est bit à bit celui qui a été recetté.
3. Application des migrations
4. Préchauffage du cache
5. Bascule du trafic
6. Test de disponibilité et contrôle des pages critiques
7. Surveillance renforcée pendant 30 minutes

## Tests avant livraison

| Type | Objet | Outil | Quand | Responsable |
|---|---|---|---|---|
| Unitaires | Une classe ou une méthode isolée : validateurs, calculs, règles de gestion | PHPUnit | À chaque push | Prestataire |
| Fonctionnels | Un parcours complet : inscription, connexion, lancement d'un exercice | PHPUnit `WebTestCase` | À chaque push | Prestataire puis Ministère |
| Non-régression | L'ensemble des tests existants, rejoué intégralement | PHPUnit via la CI | Avant chaque fusion et chaque livraison | Automatique |

**Règle de livraison** : l'échec d'un seul test de non-régression entraîne le refus de livraison, sans dérogation.

## Automatisation

Outil retenu : **GitHub Actions**, pour trois raisons — il est intégré au dépôt (aucun secret à partager avec un tiers), il est déclaratif et versionné (`.github/workflows/`, donc revu comme du code), et il est gratuit dans les limites du projet.

| Workflow | Déclencheur | Rôle |
|---|---|---|
| `ci.yml` | push, pull request | Qualité, sécurité, tests, construction de l'image |
| `cd.yml` | tag `v*.*.*` | Publication de l'artefact, préproduction, production, communication |
| `labels.yml` | modification de `labels.json` | Synchronise la configuration du ticketing |
| `dependabot.yml` | hebdomadaire | Veille automatisée sur les dépendances |

**Le déploiement n'est jamais déclenché par un push**, uniquement par un tag suivi d'une approbation manuelle pour la production. Une mise en production reste un acte volontaire et tracé.

## Communication

### Mise en production planifiée

| Échéance | Destinataires | Contenu |
|---|---|---|
| J-5 | Ministère, référents métier | Date, contenu, durée d'indisponibilité prévue |
| J-1 | Mêmes destinataires | Confirmation ou report |
| J, avant | Utilisateurs | Bandeau d'information dans l'application |
| J, après | Ministère | Version livrée, notes de version, résultat des contrôles |

### Échec de déploiement

| Niveau | Délai | Action |
|---|---|---|
| N1 — Prestataire | Immédiat | Ticket d'incident ouvert automatiquement par la CI. Vérification que la version précédente est en service. |
| N2 — Chef de projet Ministère | 30 min | Information sur la nature de la panne et le délai estimé. |
| N3 — Direction et utilisateurs | 2 h si non résolu | Communication publique, message de maintenance dans l'application. |

**Décision de retour arrière** : si le service n'est pas rétabli en 30 minutes, on repasse à la version précédente sans attendre le diagnostic. Comprendre la panne vient après le rétablissement du service.

Le retour arrière consiste à redémarrer l'image de la version précédente, toujours disponible dans le registre. Si la version défaillante a appliqué une migration destructrice, le retour arrière passe par la restauration de la sauvegarde préalable — d'où l'obligation de la vérifier avant tout déploiement.

## Configuration à réaliser sur GitHub

- [ ] Créer les environnements `preproduction` et `production` (Settings → Environments)
- [ ] Sur `production`, activer **Required reviewers** : c'est ce réglage qui matérialise l'approbation manuelle
- [ ] Renseigner les secrets d'environnement (`APP_SECRET`, `MYSQL_PASSWORD`, accès au serveur cible)
- [ ] Appliquer les règles de protection de `main` et `develop` (voir `versioning.md`)
