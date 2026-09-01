# Plan de maintenance

Outil de ticketing retenu : **GitHub Issues + GitHub Projects**.

## Pourquoi cet outil

| Critère | Justification |
|---|---|
| Continuité avec le code | Un ticket se relie à une branche, une pull request et un commit. La traçabilité « incident → correctif → livraison » est native, sans passerelle à maintenir. |
| Configuration versionnée | Formulaires (`.github/ISSUE_TEMPLATE/`) et libellés (`.github/labels.json`) sont dans le dépôt et appliqués par un workflow. La configuration du ticketing est reproductible, pas cliquée à la main. |
| Automatisation | Un échec de déploiement ouvre automatiquement un ticket d'incident ; une pull request fusionnée ferme le ticket. |
| Coût et pérennité | Gratuit, aucun serveur à administrer, aucun outil supplémentaire à faire adopter. |

Alternatives écartées : **Jira** (puissant mais surdimensionné, et impose une double saisie hors du dépôt), **Redmine** (nécessite un hébergement et une administration propres), **Trello** (aucun lien avec le code, pas de champs structurés).

## Catégories de tickets

Les quatre catégories exigées, chacune dotée d'un formulaire dédié qui impose les informations nécessaires à la qualification. Les tickets vides sont désactivés : aucun ticket n'arrive sans les éléments minimaux.

| Catégorie | Formulaire | Libellé |
|---|---|---|
| Incident technique | `01-incident-technique.yml` | `incident-technique` |
| Incident de sécurité | `02-incident-securite.yml` | `incident-securite` |
| Anomalie fonctionnelle | `03-anomalie-fonctionnelle.yml` | `anomalie-fonctionnelle` |
| Demande d'évolution | `04-demande-evolution.yml` | `evolution` |

## Cycle de vie d'un ticket

```
  Déclaration ──> Qualification ──> Reproduction ──> Évaluation
                        │                 │          gravité + priorité
                        │                 │                │
                   rejet/doublon     non reproductible     ▼
                        │                 │           Correction
                        ▼                 ▼                │
                     Fermé  <──────────────────────  Tests + non-régression
                        ▲                                  │
                        │                                  ▼
                   Validation  <────────────────────   Livraison
```

| Étape | Libellé | Ce qui est fait | Responsable |
|---|---|---|---|
| 1. Déclaration | `etape:declare` | Le demandeur remplit le formulaire. Le ticket est horodaté : le délai de prise en compte court à partir de cet instant. | Demandeur |
| 2. Qualification | `etape:qualifie` | Catégorie confirmée, doublons écartés, module identifié, gravité et priorité posées. | Prestataire |
| 3. Reproduction | `etape:reproduit` | L'incident est reproduit sur un environnement maîtrisé. Non reproductible après relance du demandeur : le ticket est fermé avec justification. | Prestataire |
| 4. Évaluation | — | Analyse de la cause racine, chiffrage de la charge, choix entre correction et contournement. Pour une évolution : proposition de prestation (analyse, délai, coût, impact documentaire). | Prestataire |
| 5. Correction | `etape:en-cours` | Branche `fix/`, `hotfix/` ou `feature/` créée depuis le ticket. | Prestataire |
| 6. Tests | `etape:en-test` | Test reproduisant le défaut corrigé, ajouté à la suite de non-régression. La CI valide l'ensemble. | Prestataire |
| 7. Livraison | `etape:livre` | Fusion, tag de version, déploiement. Le ticket porte le numéro de version livrée. | Prestataire |
| 8. Validation | — | Le demandeur confirme la résolution sur l'environnement cible. | Demandeur |
| 9. Fermeture | *fermé* | Fermeture automatique à la fusion via `Closes #n`. Sans validation sous 5 jours ouvrés, la fermeture est tacite. | Automatique |

**Point de méthode** : l'étape 6 est non négociable. Toute correction s'accompagne d'un test qui échoue avant le correctif et réussit après. C'est ce qui transforme la correction d'aujourd'hui en test de non-régression permanent, et empêche la réapparition du même défaut.

## Gravité, priorité et délais

La **gravité** mesure l'impact technique, la **priorité** arbitre l'ordre de traitement. Un incident mineur touchant tous les utilisateurs peut être prioritaire sur un incident majeur touchant un seul compte.

| Gravité | Définition | Libellé |
|---|---|---|
| Bloquant | Interruption non planifiée rendant le service inutilisable, sans solution de contournement. | `gravite:bloquant` |
| Majeur | Service inopérant par intermittence, ou utilisable seulement via un contournement. | `gravite:majeur` |
| Mineur | Simple altération de la qualité de service, le service reste opérationnel. | `gravite:mineur` |

Délais contractuels, en heures ouvrées :

| Correctif | Prise en compte et diagnostic | Correction | Libellé |
|---|---|---|---|
| Incident bloquant, priorité critique | 1 h | 3 h | `priorite:critique` |
| Incident bloquant, priorité forte | 2 h | 6 h | `priorite:forte` |
| Incident majeur | 7 h | 16 h | `priorite:normale` |
| Incidents mineurs, par lots | 1 jour | 40 h | `priorite:basse` |

Tout incident de sécurité entre par défaut en `priorite:critique`, quelle que soit sa gravité apparente : une faille sans impact visible reste exploitable.

## Maintenance évolutive

Chaque demande d'évolution donne lieu à une proposition de prestation avant tout développement, constituée de :

1. **Analyse du besoin** — reformulation du besoin métier, identification des règles de gestion touchées, critères d'acceptation vérifiables.
2. **Estimation de charge** — en jours-homme, décomposée en conception / développement / tests / documentation.
3. **Délai de mise en œuvre** — date de livraison proposée, tenant compte de la charge de maintenance corrective en cours.
4. **Impact documentaire** — liste des documents à mettre à jour. Cette mise à jour fait partie de la livraison, pas d'un travail ultérieur.
5. **Impact sur les données personnelles** — si l'évolution collecte ou expose des données nouvelles, une analyse d'impact précède le développement.

Le Ministère arbitre sur cette base. Sans accord explicite, l'évolution n'est pas développée.

**Base de chiffrage** — le budget plafond du projet est de 75 000 € pour 12 mois, ce qui situe le taux journalier de référence autour de **500 € HT**.

| Complexité | Charge indicative | Coût indicatif |
|---|---|---|
| Simple (libellé, champ, filtre) | 0,5 à 1 j | 250 à 500 € |
| Moyenne (écran, règle de gestion) | 2 à 5 j | 1 000 à 2 500 € |
| Élevée (module, refonte d'un modèle) | 8 à 20 j | 4 000 à 10 000 € |

## Tableaux de bord

Un **GitHub Project** (vue tableau) sert de pilotage, alimenté automatiquement par les libellés d'étape.

**Vue 1 — Suivi opérationnel.** Colonnes calquées sur le cycle de vie : Déclaré, Qualifié, Reproduit, En cours, En test, Livré. Chaque carte affiche gravité, priorité, module et échéance.

**Vue 2 — Respect des délais.** Filtrée sur `priorite:critique` et `priorite:forte`, triée par date de création. Fait apparaître immédiatement tout ticket approchant sa limite contractuelle.

**Vue 3 — Répartition.** Groupée par catégorie et par module. Un module concentrant les anomalies signale une dette technique à traiter à la racine plutôt qu'incident par incident.

**Indicateurs suivis mensuellement**

| Indicateur | Cible |
|---|---|
| Taux de respect des délais de prise en compte | ≥ 95 % |
| Taux de respect des délais de correction | ≥ 90 % |
| Délai médian de résolution, par gravité | Stable ou en baisse |
| Taux de réouverture après validation | ≤ 5 % |
| Part des corrections couvertes par un test de non-régression | 100 % |
| Nombre d'incidents de sécurité ouverts | 0 en fin de mois |

Un taux de réouverture élevé signale une validation trop rapide ou une cause racine non traitée ; c'est l'indicateur à surveiller en priorité.

## Configuration à réaliser sur GitHub

- [ ] Lancer le workflow **Synchronisation des libellés** (onglet Actions → Run workflow) pour créer les 29 libellés
- [ ] Créer un GitHub Project « Maintenance CESIZen » et y ajouter les trois vues décrites ci-dessus
- [ ] Activer l'ajout automatique des nouveaux tickets au projet (Project → Workflows → Auto-add to project)
- [ ] Activer les avis de sécurité privés (Settings → Advanced Security → Private vulnerability reporting)
