# Veille technologique

## Ce que la veille doit produire

Une veille qui se contente d'accumuler des liens ne sert à rien. Celle de CESIZen a trois finalités précises :

1. **Ne jamais découvrir une faille par son exploitation.** L'audit initial du projet a révélé 42 avis de sécurité non traités, dont un contournement de firewall touchant directement le mécanisme d'autorisation de l'application. Ils étaient publics depuis des mois.
2. **Anticiper les fins de support.** Symfony 6.4 est une version à support long terme : correctifs de sécurité jusqu'en **novembre 2027**. Passé cette date, une faille non corrigée reste ouverte indéfiniment. La migration se prépare des mois à l'avance, pas la veille de l'échéance.
3. **Alimenter la maintenance évolutive.** Une pratique nouvelle ou un outil pertinent devient une demande d'évolution chiffrée, pas une refonte improvisée.

## Sources suivies

### Sécurité — priorité absolue

| Source | Ce qu'elle apporte | Fréquence | Mode |
|---|---|---|---|
| [Avis de sécurité Symfony](https://symfony.com/blog/category/security-advisories) | Failles du framework, publiées avec le correctif | À chaque publication | Flux RSS |
| [GitHub Advisory Database](https://github.com/advisories?query=ecosystem%3Acomposer) | Failles de l'écosystème PHP | Continu | Dependabot |
| [CERT-FR](https://www.cert.ssi.gouv.fr/) | Alertes nationales, contexte des attaques visant le secteur public | Quotidien | Flux RSS |
| [OWASP Top 10](https://owasp.org/www-project-top-ten/) | Référentiel des risques applicatifs | À chaque révision | Consultation |
| `composer audit` | Confrontation des dépendances réelles aux avis publiés | À chaque exécution de la CI | Automatique, bloquant |

Le CERT-FR mérite une attention particulière : le projet étant porté par un ministère, les alertes visant les administrations concernent directement le contexte de déploiement, pas seulement la technique.

### Écosystème technique

| Source | Ce qu'elle apporte | Fréquence |
|---|---|---|
| [Blog Symfony](https://symfony.com/blog/) | Nouveautés, calendrier des versions, fins de support | Hebdomadaire |
| [Calendrier des versions PHP](https://www.php.net/supported-versions.php) | Dates de fin de support de PHP 8.2 | Trimestriel |
| [Blog Doctrine](https://www.doctrine-project.org/blog/) | Évolutions de l'ORM, ruptures annoncées | Mensuel |
| [Notes de version Docker](https://docs.docker.com/engine/release-notes/) | Évolutions de l'infrastructure | Trimestriel |
| Changelog GitHub Actions | Dépréciations des actions utilisées par la CI | Mensuel |

### Réglementaire et métier

| Source | Ce qu'elle apporte | Fréquence |
|---|---|---|
| [Actualités CNIL](https://www.cnil.fr/fr/actualites) | Doctrine sur les données de santé, sanctions, recommandations | Hebdomadaire |
| [Référentiel général de sécurité (ANSSI)](https://cyber.gouv.fr/) | Exigences applicables au secteur public | Trimestriel |
| [RGAA — accessibilité](https://accessibilite.numerique.gouv.fr/) | Obligation légale pour un service public | Semestriel |
| Santé mentale — publications ministérielles | Évolution des recommandations sur les outils de gestion du stress | Trimestriel |

La source CNIL n'est pas décorative : une décision de sanction sur un traitement de données de santé peut rendre non conforme une pratique jusque-là admise.

## Rythme

| Cadence | Durée | Contenu |
|---|---|---|
| **Continu** | Automatique | Dependabot et `composer audit` signalent toute vulnérabilité connue |
| **Hebdomadaire** | 30 min | Lecture des flux sécurité et CNIL. Traitement des demandes de fusion Dependabot |
| **Mensuel** | 1 h | Écosystème technique. Bilan des mises à jour, arbitrage des sujets en attente |
| **Trimestriel** | 2 h | Revue du calendrier de fin de support, réévaluation de la matrice de risques |
| **Annuel** | 1 j | Bilan : dette technique, migrations à planifier, budget de maintenance |

Le créneau hebdomadaire est court et fixe, précisément pour être tenu. Une veille programmée à deux heures par semaine est abandonnée au premier mois chargé.

## Critères de sélection

Une information n'est retenue que si elle passe **quatre filtres successifs**. Si l'un échoue, elle est écartée.

**1. Applicabilité** — La technologie concernée est-elle réellement utilisée par CESIZen ? Une faille dans un composant Symfony non installé ne concerne pas le projet.

**2. Criticité** — Quel est l'impact si l'on ne fait rien ? On reprend l'échelle du plan de sécurisation : gravité × probabilité × capacité d'anticipation.

**3. Coût de mise en œuvre** — Charge estimée, risque de régression, impact documentaire. Une amélioration marginale coûtant dix jours-homme n'est pas retenue.

**4. Alignement au besoin** — La demande émane-t-elle du besoin du ministère ou de l'attrait pour une technologie ? Ce filtre écarte les migrations motivées par la mode.

### Grille de décision

| Situation | Décision | Délai |
|---|---|---|
| Faille critique sur un composant utilisé | Correctif immédiat, hors cycle | Selon les délais du plan de maintenance |
| Faille moyenne ou faible | Ticket d'évolution, traité au cycle suivant | 1 mois |
| Fin de support à moins de 12 mois | Migration planifiée et budgétée | Selon l'échéance |
| Nouvelle fonctionnalité du framework utile | Ticket d'évolution chiffré | Arbitrage ministère |
| Nouveauté sans besoin identifié | Écartée, consignée pour mémoire | — |

## Automatisation

Une partie de la veille est déléguée à l'outillage, ce qui garantit qu'elle a lieu même lors d'une période chargée.

| Dispositif | Rôle | Fichier |
|---|---|---|
| Dependabot | Ouvre une demande de fusion à chaque mise à jour disponible, sur les dépendances PHP, npm, Docker et GitHub Actions | `.github/dependabot.yml` |
| `composer audit` | Confronte les dépendances aux avis de sécurité publiés, **bloque la chaîne d'intégration** en cas de vulnérabilité | `.github/workflows/ci.yml` |
| Suite de non-régression | Valide chaque montée de version avant fusion | `.github/workflows/ci.yml` |

Le point important est l'enchaînement : Dependabot propose, la chaîne d'intégration vérifie, un humain décide. Une montée de version n'est jamais fusionnée sans que les 50 tests soient passés — ce qui rend la veille applicable sans crainte de régression.

Les mises à jour majeures de Symfony sont explicitement exclues de l'automatisation : elles relèvent d'une migration planifiée, pas d'une mise à jour de routine.

## Comment la veille alimente la maintenance

```
   Source          Filtrage             Décision              Traçabilité
   ───────         ────────             ────────              ───────────
   Flux RSS   ──┐
   CERT-FR    ──┤   4 critères      ┌── Correctif urgent ──> ticket incident-securite
   CNIL       ──┼──> applicabilité  ┤
   Dependabot ──┤    criticité      ├── Évolution ─────────> ticket evolution + chiffrage
   composer   ──┘    coût           │                        (analyse, délai, coût, doc)
   audit             alignement     └── Écartée ───────────> consignée, non traitée
```

Toute information retenue devient un ticket portant le libellé `veille`. Rien ne reste à l'état de note personnelle : c'est ce qui rend la veille vérifiable et transmissible à un prestataire repreneur.

Pour une demande d'évolution issue de la veille, le ticket comporte les mêmes éléments que toute évolution : analyse du besoin, charge estimée, délai, impact documentaire et coût. Le ministère arbitre sur cette base.

## Suivi

| Indicateur | Cible | Ce qu'il révèle |
|---|---|---|
| Délai de traitement d'une faille critique | ≤ 48 h | Réactivité réelle du dispositif |
| Vulnérabilités connues non traitées | 0 | État de la dette de sécurité |
| Demandes Dependabot en attente de plus de 30 jours | ≤ 2 | Un empilement signale une veille abandonnée |
| Composants à moins de 12 mois du support | Suivi nominatif | Anticipation des migrations |
| Sujets de veille convertis en tickets | Suivi trimestriel | Utilité effective de la veille |

L'indicateur le plus révélateur est le troisième : des demandes Dependabot qui s'accumulent signifient que le dispositif automatique tourne mais que plus personne ne le traite. C'est le premier symptôme d'une veille morte.

## Échéances connues

| Composant | Version | Fin de support | À anticiper |
|---|---|---|---|
| Symfony | 6.4 LTS | Novembre 2027 | Migration vers la LTS suivante à préparer début 2027 |
| PHP | 8.2 | Décembre 2026 (sécurité) | Montée vers 8.3 ou 8.4 à planifier en 2026 |
| MySQL | 8.0 | Avril 2026 | Montée vers 8.4 LTS à planifier |
| Doctrine ORM | 3.x | Suivi continu | Pas d'échéance annoncée |

Ces trois premières échéances tombent dans un intervalle de dix-huit mois. Elles doivent être budgétées ensemble dans le plan de maintenance plutôt que traitées en urgence l'une après l'autre.
