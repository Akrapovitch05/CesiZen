# Gestion des versions

Outil retenu : **Git**, dépôt distant hébergé sur **GitHub**.

## Pourquoi Git et GitHub

| Critère | Justification |
|---|---|
| Modèle décentralisé | Chaque poste détient l'historique complet : une panne du serveur central ne fait perdre aucune source. |
| Standard du marché | Compétence immédiatement disponible chez tout prestataire repreneur — un enjeu de pérennité pour le Ministère. |
| Outil unique | GitHub couvre le versioning, l'intégration continue (Actions), le ticketing (Issues) et la documentation (Markdown versionné). Le cahier des charges signale explicitement qu'un outil unique capable de gérer versioning, tests unitaires et non-régression serait apprécié. |
| Coût | Gratuit pour un dépôt de cette taille, y compris les 2 000 minutes mensuelles d'exécution CI. |
| Traçabilité | Chaque ligne livrée est rattachable à un commit, une pull request, un relecteur et un ticket. |

Alternatives écartées : **GitLab** (fonctionnellement équivalent, mais impose d'héberger et de maintenir une instance ou de gérer un second compte pour le seul CI/CD) et **SVN** (modèle centralisé, gestion des branches trop coûteuse pour un rythme de livraison hebdomadaire).

## Modèle de branches

Un **GitHub Flow enrichi d'une branche d'intégration**, dimensionné pour une équipe réduite.

```
main         ●───────────────●───────────────●        production, taggée
              \             /               /
develop        ●───●───●───●───●───●───●───●          intégration continue
                    \     /     \     /
feature/…            ●───●       ●───●                développement
hotfix/…                      ●──────────────●        correction urgente
```

| Branche | Rôle | Source | Fusionnée dans |
|---|---|---|---|
| `main` | Reflète exactement ce qui tourne en production. Chaque commit est taggé. | — | — |
| `develop` | Intégration des développements terminés. Base de la préproduction. | `main` | `main` (à la livraison) |
| `feature/<ticket>-<intitulé>` | Une fonctionnalité ou une évolution. | `develop` | `develop` |
| `fix/<ticket>-<intitulé>` | Correction d'une anomalie non urgente. | `develop` | `develop` |
| `hotfix/<ticket>-<intitulé>` | Correction d'un incident bloquant en production. | `main` | `main` **et** `develop` |
| `chore/<intitulé>` | Outillage, CI, dépendances, documentation. | `develop` | `develop` |

**Règle du hotfix** : c'est le seul cas où l'on part de `main` plutôt que de `develop`. Il est impératif de le refusionner ensuite dans `develop`, faute de quoi la correction serait perdue à la livraison suivante.

Exemples : `feature/42-tracker-emotions`, `fix/57-mot-de-passe-oublie`, `hotfix/61-fuite-session-admin`.

## Règles de travail

1. **Aucun commit direct sur `main` ni sur `develop`.** Toute modification passe par une pull request. La protection de branche est décrite plus bas.
2. **Une branche = un ticket.** Le numéro figure dans le nom de la branche et dans la pull request. Sans ticket, pas de branche.
3. **Une pull request est fusionnée uniquement si** la CI est verte et qu'une revue a été faite.
4. **Mode de fusion : squash.** L'historique de `develop` reste linéaire, un commit par fonctionnalité livrée.
5. **La branche est supprimée après fusion.** Le dépôt ne conserve que les branches vivantes.
6. **Rebase plutôt que merge** pour mettre à jour une branche de travail sur `develop`, afin d'éviter les commits de fusion parasites.

## Convention de messages de commit

Format **Conventional Commits** : `<type>(<portée>): <description à l'impératif>`

| Type | Usage |
|---|---|
| `feat` | Nouvelle fonctionnalité |
| `fix` | Correction d'anomalie |
| `security` | Correctif de sécurité |
| `docs` | Documentation seule |
| `test` | Ajout ou modification de tests |
| `refactor` | Réécriture sans changement de comportement |
| `perf` | Amélioration de performance |
| `chore` | Outillage, dépendances, configuration |

```
feat(respiration): ajouter le cycle 7-4-8 a l exercice de coherence cardiaque

Les trois cycles prevus au cahier des charges sont desormais configurables
depuis le back-office plutot que codes en dur.

Closes #42
```

Cette convention n'est pas décorative : elle permet de générer automatiquement les notes de version à partir de l'historique, et de repérer immédiatement les commits `security` lors d'un audit.

## Numérotation des versions

**Versionnage sémantique** : `MAJEUR.MINEUR.CORRECTIF`

| Incrément | Quand | Exemple |
|---|---|---|
| `MAJEUR` | Rupture de compatibilité (API, schéma de données non rétrocompatible) | `1.4.2` → `2.0.0` |
| `MINEUR` | Nouvelle fonctionnalité rétrocompatible | `1.4.2` → `1.5.0` |
| `CORRECTIF` | Correction d'anomalie ou de sécurité | `1.4.2` → `1.4.3` |

Le tag est posé sur `main` et **déclenche seul le déploiement** (voir `.github/workflows/cd.yml`). Une mise en production est donc toujours un acte volontaire, jamais la conséquence d'un simple push.

```bash
git checkout main && git pull
git tag -a v1.5.0 -m "Module tracker d emotions"
git push origin v1.5.0
```

## Versioning de la documentation

La documentation vit dans le dépôt, au format Markdown, dans `docs/`. Elle suit donc le même cycle que le code : même branche, même pull request, même revue, même tag. Une évolution qui modifie un comportement documenté et sa documentation sont livrées ensemble, ce qui rend impossible la dérive entre les deux.

| Document | Contenu |
|---|---|
| `README.md` | Présentation et guide d'installation |
| `docs/versioning.md` | Le présent document |
| `docs/deploiement.md` | Environnements, étapes et procédures de déploiement |
| `docs/maintenance.md` | Cycle de vie des tickets, délais, tableaux de bord |
| `docs/veille.md` | Sources, fréquence et exploitation de la veille |
| `docs/securite.md` | Risques, mesures et gestion de crise |

## Protection des branches à configurer sur GitHub

À appliquer dans **Settings → Branches → Add branch ruleset** pour `main` et `develop` :

- [ ] Interdire le push direct (« Restrict updates »)
- [ ] Exiger une pull request avant fusion, avec **1 approbation** minimum
- [ ] Exiger que les contrôles suivants réussissent : `Qualité du code`, `Sécurité des dépendances`, `Tests automatisés`, `Construction de l'image de production`
- [ ] Exiger que la branche soit à jour avec la cible avant fusion
- [ ] Interdire la réécriture d'historique (« Restrict force pushes »)
- [ ] Interdire la suppression de la branche

> En projet individuel, l'exigence d'approbation est à activer avec l'option
> permettant à l'auteur de valider sa propre pull request, sans quoi aucune
> fusion ne serait possible. La règle reste en place pour être immédiatement
> opérante dès qu'un second développeur rejoint le projet.
