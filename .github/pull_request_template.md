## Ticket lié

<!-- Toute pull request répond à un ticket. Ferme-le automatiquement à la fusion. -->
Closes #

## Nature de la modification

- [ ] Correction d'un incident ou d'une anomalie (maintenance corrective)
- [ ] Nouvelle fonctionnalité (maintenance évolutive)
- [ ] Correctif de sécurité
- [ ] Infrastructure, CI/CD ou outillage
- [ ] Documentation

## Description

<!-- Ce qui change, et pourquoi. -->

## Vérifications avant demande de revue

- [ ] La branche part de `develop` et respecte la convention de nommage
- [ ] Les messages de commit suivent la convention (`docs/versioning.md`)
- [ ] La chaîne d'intégration continue est au vert
- [ ] Des tests couvrent la modification (et le cas de non-régression, s'il s'agit d'une correction)
- [ ] La suite de tests complète passe en local (`.\cesizen.ps1 test`)
- [ ] Aucun secret, jeton ni donnée personnelle n'est introduit dans le code ou les tests
- [ ] La documentation impactée est mise à jour

## Impact sur les données personnelles

<!-- CESIZen traite des données de santé mentale, donc sensibles au sens du RGPD.
     Répondre « Aucun » si la modification n'y touche pas. -->

- Données concernées :
- Base légale et durée de conservation inchangées : oui / non
- Analyse d'impact nécessaire : oui / non

## Vérification manuelle effectuée

<!-- Comment le relecteur peut reproduire la validation. -->

1.
2.

## Retour arrière

<!-- Comment annuler cette modification si elle pose problème en production. -->
