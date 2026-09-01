# Plan de sécurisation

## Pourquoi le niveau d'exigence est élevé

CESIZen traite des données révélant l'état de santé mentale de ses utilisateurs : diagnostics de stress, séances de respiration, activités de détente consultées. L'article 9 du RGPD classe les données de santé parmi les **catégories particulières**, dont le traitement est interdit par principe et n'est autorisé que par exception, sous conditions renforcées.

Une fuite ici ne coûte pas un mot de passe : elle expose l'état psychologique d'une personne identifiable, avec des conséquences possibles sur son emploi, ses assurances ou sa vie privée. À cela s'ajoute que le projet est porté par un ministère, ce qui en fait une cible d'intérêt pour des acteurs motivés politiquement.

## Méthode d'analyse des risques

Chaque risque est coté sur trois axes, notés de 1 à 4.

**Gravité (G)** — ampleur des conséquences si le risque se réalise.

| Note | Niveau | Signification |
|---|---|---|
| 1 | Négligeable | Gêne passagère, aucune donnée touchée |
| 2 | Modérée | Service dégradé, données non sensibles concernées |
| 3 | Grave | Interruption de service ou fuite de données personnelles |
| 4 | Critique | Fuite de données de santé, atteinte durable aux personnes |

**Probabilité (P)** — vraisemblance d'occurrence sur une année.

| Note | Niveau | Signification |
|---|---|---|
| 1 | Rare | Nécessite des moyens importants ou un concours de circonstances |
| 2 | Possible | Déjà observé sur des applications comparables |
| 3 | Probable | Attendu au moins une fois par an |
| 4 | Fréquent | Permanent, automatisé par des robots |

**Capacité d'anticipation (A)** — aptitude à détecter ou prévenir avant l'impact. La note est **inversée** : plus on est aveugle, plus elle est haute, car un risque invisible est plus dangereux qu'un risque surveillé.

| Note | Niveau | Signification |
|---|---|---|
| 1 | Très bonne | Détection automatique et alerte immédiate |
| 2 | Bonne | Détecté par la supervision ou les tests |
| 3 | Faible | Détecté a posteriori, à l'analyse des journaux |
| 4 | Nulle | Peut passer inaperçu durablement |

**Criticité = G × P × A**, de 1 à 64.

| Criticité | Niveau | Conduite à tenir |
|---|---|---|
| 1 – 8 | Faible | Risque accepté, surveillé |
| 9 – 23 | Modérée | À traiter dans le cycle normal |
| 24 – 39 | Élevée | Traitement prioritaire, mesures avant mise en production |
| 40 – 64 | Critique | Inacceptable. Mise en production bloquée tant que la criticité n'est pas réduite |

## Matrice des risques

La colonne **Avant** correspond à l'état constaté sur l'application au démarrage de l'activité 3. La colonne **Après** correspond à l'état une fois les mesures appliquées. Les risques marqués d'un astérisque ont été **effectivement constatés sur le code du projet**, et non supposés.

| # | Risque | Causes possibles | Conséquences | G | P | A | **Avant** | **Après** |
|---|---|---|---|---|---|---|---|---|
| R1 | **Exposition des comptes par l'API\*** | Ressource exposée sans règle d'accès | Fuite de l'annuaire complet des utilisateurs, hachages compris | 4 | 4 | 3 | **48** | **4** |
| R2 | **Fuite de données de santé** | Contrôle d'accès insuffisant, injection, sauvegarde non chiffrée | Atteinte à la vie privée, sanction CNIL, perte de confiance | 4 | 3 | 3 | **36** | **8** |
| R3 | **Élévation de privilèges\*** | Gestion des rôles défaillante, un seul rôle stockable | Un utilisateur simple accède au back-office | 4 | 2 | 3 | **24** | **4** |
| R4 | **Attaque par force brute** | Absence de limitation des tentatives | Compromission de comptes, dont des comptes administrateurs | 4 | 4 | 2 | **32** | **4** |
| R5 | **Mots de passe faibles\*** | Aucune politique de complexité | Comptes devinables, effet domino par réutilisation | 3 | 4 | 3 | **36** | **6** |
| R6 | **Secret applicatif versionné\*** | `APP_SECRET` en clair dans un fichier suivi par Git | Forge de jetons CSRF et de cookies de session | 4 | 3 | 4 | **48** | **4** |
| R7 | **Dépendances vulnérables\*** | Absence de veille, 42 avis non traités | Exploitation d'une faille publique, dont un contournement de firewall | 4 | 4 | 3 | **48** | **6** |
| R8 | **Hameçonnage des utilisateurs** | Usurpation de l'identité visuelle du ministère | Vol d'identifiants, accès aux données de santé | 3 | 3 | 4 | **36** | **18** |
| R9 | **Falsification de requête (CSRF)** | Formulaires sensibles non protégés | Actions exécutées à l'insu de l'utilisateur | 3 | 3 | 3 | **27** | **6** |
| R10 | **Injection SQL** | Concaténation de requêtes | Lecture ou destruction de la base | 4 | 2 | 2 | **16** | **4** |
| R11 | **Injection de script (XSS)** | Sortie non échappée | Vol de session, défiguration | 3 | 3 | 2 | **18** | **6** |
| R12 | **Perte de données** | Panne matérielle, erreur humaine, rançongiciel | Perte définitive du journal d'émotions des utilisateurs | 4 | 2 | 2 | **16** | **8** |
| R13 | **Indisponibilité du service** | Déni de service, panne d'hébergement | Service inaccessible, atteinte à l'image du ministère | 3 | 3 | 2 | **18** | **12** |
| R14 | **Déploiement défaillant\*** | Migrations incohérentes avec le code | Application livrée non fonctionnelle | 3 | 4 | 2 | **24** | **6** |
| R15 | **Compte administrateur compromis** | Poste infecté, mot de passe réutilisé | Contrôle total de la plateforme | 4 | 2 | 3 | **24** | **12** |
| R16 | **Non-conformité RGPD** | Absence de registre, conservation illimitée | Sanction pouvant atteindre 4 % du chiffre d'affaires | 4 | 3 | 3 | **36** | **9** |
| R17 | **Fuite par les environnements de test** | Copie de données réelles en préproduction | Exposition de données de santé à un public élargi | 4 | 3 | 3 | **36** | **6** |
| R18 | **Divulgation d'information technique** | Traces d'erreur affichées en production | Reconnaissance facilitée pour un attaquant | 2 | 3 | 3 | **18** | **4** |

**Lecture.** Six risques étaient en zone critique ou élevée, dont quatre effectivement présents dans le code. Aucun ne subsiste au-dessus de 18. Le risque résiduel le plus haut reste l'hameçonnage (R8, 18) : il ne dépend pas du code mais du comportement des utilisateurs, et ne peut être réduit que par la sensibilisation.

## Mesures par risque

### R1 — Exposition des comptes par l'API

*Constaté.* L'entité `Utilisateur` portait `#[ApiResource]` sans aucune restriction : `GET /api/utilisateurs` répondait à un visiteur anonyme.

**Préventif** — Chaque opération est déclarée individuellement avec `security: is_granted('ROLE_ADMIN')`, en lecture seule. Un groupe de sérialisation restreint les champs exposés : le mot de passe haché n'appartient à aucun groupe, il ne peut donc jamais être sérialisé. `access_control` ajoute une seconde barrière interdisant toute écriture anonyme sur `/api`.

**Correctif** — Révocation des sessions actives, analyse des journaux d'accès pour mesurer l'exposition réelle, notification CNIL si des données ont été consultées.

**Vérification** — `ApiSecuriteTest` : refus aux anonymes, refus aux utilisateurs simples, absence de `password` et de tout préfixe `$2y$` dans les réponses.

### R2 — Fuite de données de santé

**Préventif** — HTTPS obligatoire de bout en bout. Base de données sans port publié, joignable uniquement par le réseau interne. Chiffrement au repos du volume et des sauvegardes. Principe du moindre privilège : le compte applicatif MySQL n'a pas les droits d'administration. Cloisonnement strict des rôles, vérifié par les tests.

**Correctif** — Isolement du service, conservation des journaux pour l'analyse, notification CNIL sous 72 h, information individuelle des personnes concernées lorsque le risque est élevé.

### R3 — Élévation de privilèges

*Constaté.* Le champ `roles` était une chaîne de 50 caractères ne pouvant porter qu'un seul rôle ; `getRoles()` pouvait renvoyer un tableau contenant `null`.

**Préventif** — Rôles stockés en JSON, `ROLE_USER` garanti à tout compte authentifié. Une inscription publique ne confère jamais autre chose que `ROLE_USER`. L'attribution de `ROLE_ADMIN` passe exclusivement par le back-office, et chaque modification est journalisée.

**Vérification** — Tests unitaires sur la logique de rôles, tests fonctionnels du cloisonnement (`403` attendu sur `/admin` pour un `ROLE_USER`).

### R4 — Attaque par force brute

**Préventif** — `login_throttling` : 5 tentatives par quart d'heure, appliquées simultanément par couple adresse IP + identifiant et par adresse IP seule, ce qui couvre aussi le balayage d'identifiants. Limitation également à l'inscription : 5 comptes par heure et par adresse IP. Chaque blocage est journalisé dans le canal `securite`.

**Correctif** — Blocage de la plage d'adresses au niveau du pare-feu, notification du titulaire du compte visé, réinitialisation forcée en cas de succès suspecté.

### R5 — Mots de passe faibles

*Constaté.* Aucune contrainte : un compte pouvait être créé avec le mot de passe `a`.

**Préventif** — Minimum 12 caractères, avec minuscule, majuscule, chiffre et caractère spécial. Contrainte `NotCompromisedPassword` : le mot de passe est confronté à la base *Have I Been Pwned* par *k-anonymity*, sans que le mot de passe ni son empreinte complète ne quittent le serveur. Hachage bcrypt via `auto`, qui basculera sur un algorithme plus robuste sans changement de code. Saisie confirmée pour éviter les erreurs de frappe.

**Vérification** — Cinq cas d'échec testés individuellement : trop court, sans majuscule, sans minuscule, sans chiffre, sans caractère spécial.

### R6 — Secret applicatif versionné

*Constaté.* `APP_SECRET` figurait en clair dans `.env`, fichier suivi par Git.

**Préventif** — `.env` ne contient plus que des valeurs par défaut. Les secrets vivent dans `.env.local`, ignoré par Git ; en production ils sont injectés par variables d'environnement. Un contrôle d'intégration continue échoue si une valeur de secret est détectée dans un fichier suivi — ce contrôle a été testé dans les deux sens : il laisse passer les gabarits et détecte une vraie fuite.

**Correctif** — Le secret a été régénéré. La valeur d'origine demeure dans l'historique Git et doit être considérée comme définitivement compromise : elle ne doit jamais être réutilisée.

### R7 — Dépendances vulnérables

*Constaté.* 42 avis de sécurité sur 17 paquets, dont `symfony/security-http` CVE-2026-48489, un contournement de firewall donnant accès à des routes protégées par `access_control` — exactement le mécanisme utilisé par l'application.

**Préventif** — `composer audit` bloquant en intégration continue. Dependabot ouvre une demande de fusion hebdomadaire par écosystème, chacune passant par la chaîne complète de tests. Les actions GitHub sont elles aussi surveillées : une action non maintenue est un vecteur d'attaque sur la chaîne de construction.

**Correctif** — Mise à jour appliquée dans les contraintes existantes, sans changement de version majeure. `composer audit` ne remonte plus aucun avis.

### R8 — Hameçonnage

**Préventif** — SPF, DKIM et DMARC sur le domaine d'envoi. L'application n'envoie jamais de courriel demandant un mot de passe. Bandeau de sensibilisation rappelant que l'équipe ne demande jamais d'identifiants. Charte informatique déjà produite dans le dossier de conception.

**Correctif** — Signalement du domaine usurpé, communication publique, réinitialisation des comptes touchés.

**Risque résiduel assumé.** Aucune mesure technique ne protège complètement d'un utilisateur qui saisit volontairement ses identifiants sur un site tiers. C'est le risque résiduel le plus élevé du projet.

### R9 — Falsification de requête

**Préventif** — Jetons CSRF sur tous les formulaires, y compris connexion et déconnexion, qui en étaient dépourvues. Cookie de session en `SameSite=Lax`, qui coupe la majorité des requêtes inter-sites.

**Vérification** — La présence du jeton est testée sur les formulaires d'inscription et de connexion. Un test manuel a confirmé qu'une requête sans jeton valide est rejetée en `400`.

### R10 — Injection SQL

**Préventif** — Doctrine ORM avec requêtes préparées : les valeurs ne sont jamais concaténées au SQL. Aucune requête native dans le projet. Validation systématique des entrées par les contraintes Symfony.

### R11 — Injection de script

**Préventif** — Twig échappe automatiquement toute sortie. En-têtes `X-Content-Type-Options: nosniff` et `X-Frame-Options: SAMEORIGIN`. Cookie de session en `HttpOnly` : même en cas de XSS, la session n'est pas lisible en JavaScript.

**Évolution prévue** — Une politique de sécurité de contenu (CSP) stricte reste à mettre en place ; elle demande d'inventorier les scripts inclus.

### R12 — Perte de données

**Préventif** — Sauvegarde quotidienne chiffrée, conservée 30 jours, sur un support distinct de la production. Sauvegarde systématique avant toute mise en production, et déploiement interrompu si elle n'est pas vérifiée. **Test de restauration mensuel** : une sauvegarde jamais restaurée n'est pas une sauvegarde.

**Correctif** — Restauration depuis le dernier point sain, analyse de l'écart, communication sur la perte éventuelle.

### R13 — Indisponibilité

**Préventif** — Limitation de débit en amont, redémarrage automatique des conteneurs, limites de ressources empêchant qu'un service en emporte un autre, supervision de la disponibilité.

**Correctif** — Retour à la version précédente si le déploiement est en cause, page de maintenance, communication selon la procédure d'escalade.

### R14 — Déploiement défaillant

*Constaté.* Les migrations produisaient un schéma incompatible avec les entités : sur une base fraîche, l'inscription échouait et aucun compte ne pouvait être créé.

**Préventif** — Migration de référence régénérée depuis le mapping Doctrine. `doctrine:schema:validate` vérifie la cohérence. Les tests fonctionnels s'exécutent contre une base réellement migrée, ce qui rend un tel écart impossible à livrer sans détection. Tables de jointure renommées explicitement : `Asso_5` fonctionnait sous Windows où MySQL ignore la casse, mais aurait cassé sous Linux.

### R15 — Compte administrateur compromis

**Préventif** — Nombre d'administrateurs limité au strict nécessaire. Toute action d'administration sur un compte est journalisée avec l'auteur, la cible et l'horodatage. Expiration de session après une heure d'inactivité.

**Évolution prévue** — Authentification à double facteur sur les comptes administrateurs. C'est la mesure la plus rentable pour abaisser encore ce risque.

### R16 — Non-conformité RGPD

Traitée en détail dans la section suivante.

### R17 — Fuite par les environnements de test

**Préventif** — La préproduction ne reçoit **jamais** de données réelles : la copie est anonymisée à l'export. L'environnement de test utilise une base en mémoire, détruite à l'arrêt. Les jeux de données de démonstration sont entièrement fictifs.

### R18 — Divulgation d'information technique

*Constaté.* Une erreur d'API renvoyait une trace complète avec les chemins de fichiers du serveur.

**Préventif** — `display_errors = Off` et `APP_DEBUG=0` en production, erreurs écrites dans les journaux et non dans la réponse. `expose_php = Off` et `server_tokens off` masquent les versions de PHP et de nginx. Un test vérifie qu'une URL inconnue renvoie `404` et non `500`.

## Gestion des données personnelles et RGPD

### Registre des traitements

| Traitement | Finalité | Base légale | Données | Conservation |
|---|---|---|---|---|
| Comptes utilisateurs | Permettre l'accès personnalisé | Consentement (art. 6.1.a) | Nom, prénom, adresse électronique, mot de passe haché | 2 ans après la dernière connexion |
| Séances de respiration | Suivre la pratique de l'utilisateur | Consentement explicite (art. 9.2.a) | Exercices réalisés, horodatage | 2 ans, ou suppression immédiate à la demande |
| Activités favorites | Personnaliser les suggestions | Consentement | Associations compte / activité | Durée de vie du compte |
| Journaux de sécurité | Détecter et analyser les incidents | Intérêt légitime (art. 6.1.f) | Adresse IP, identifiant, horodatage, action | 12 mois |

Les données de séances relèvent de l'article 9 : leur traitement repose sur le **consentement explicite**, recueilli séparément de l'acceptation des conditions générales, et révocable à tout moment.

### Principes appliqués

**Minimisation** — Seules quatre données personnelles sont collectées à l'inscription. Aucune date de naissance, aucune adresse postale, aucun numéro de téléphone : rien qui ne serve directement une fonctionnalité.

**Limitation de conservation** — Un compte inactif depuis 2 ans est signalé, puis supprimé après relance sans réponse. Les journaux de sécurité sont purgés à 12 mois.

**Intégrité et confidentialité** — Mots de passe hachés avec bcrypt, jamais réversibles. Transport chiffré en HTTPS. Sauvegardes chiffrées.

**Localisation** — Hébergement dans l'Union européenne. **Aucun transfert hors UE**, conformément à l'exigence explicite du cahier des charges. Ce critère est éliminatoire dans le choix de tout prestataire.

**Protection dès la conception** — La restriction d'accès à l'API, l'exclusion du mot de passe de la sérialisation et l'anonymisation de la préproduction sont des choix de conception, pas des correctifs ajoutés après coup.

### Droits des personnes

| Droit | Mise en œuvre | Délai |
|---|---|---|
| Accès | Export des données du compte au format JSON depuis le profil | Immédiat |
| Rectification | Modification directe depuis le profil | Immédiat |
| Effacement | Suppression du compte depuis le profil, propagée aux données liées | 30 jours |
| Portabilité | Même export que le droit d'accès, format structuré et lisible | Immédiat |
| Opposition | Retrait du consentement au suivi, sans perte de l'accès au compte | Immédiat |
| Limitation | Gel du compte sur demande | 72 h |

Toute demande non traitée automatiquement passe par un ticket dédié, avec un délai de réponse contractuel d'un mois.

### Analyse d'impact

Le traitement portant sur des données de santé et visant le grand public à grande échelle, une **analyse d'impact relative à la protection des données est obligatoire** (art. 35 du RGPD) avant la mise en production. Elle doit être conduite avec le délégué à la protection des données du ministère.

Toute évolution introduisant une nouvelle donnée personnelle déclenche une réévaluation : le formulaire de demande d'évolution comporte pour cette raison un champ obligatoire sur l'impact données personnelles.

## Gestion de crise

### Niveaux d'escalade

| Niveau | Déclencheur | Qui est mobilisé | Délai | Actions |
|---|---|---|---|---|
| **N1** | Anomalie de sécurité sans impact avéré : tentative bloquée, dépendance vulnérable | Équipe de développement | 1 h ouvrée | Qualification, correction, journalisation |
| **N2** | Incident avéré sans fuite confirmée : compte compromis, intrusion bloquée | N1 + référent sécurité | 1 h, 24 h/24 | Isolement, préservation des preuves, analyse d'étendue |
| **N3** | Fuite de données personnelles avérée ou probable | N2 + direction + DPO + communication | Immédiat | Cellule de crise, notification CNIL, information des personnes |

**Règle de déclenchement** : le doute fait monter d'un niveau. Une escalade injustifiée coûte une réunion ; une escalade manquée coûte le délai de 72 heures.

### Chronologie d'un incident de niveau N3

| Échéance | Action | Responsable |
|---|---|---|
| T+0 | Détection, ouverture d'un ticket `incident-securite` | Détecteur |
| T+15 min | Qualification, mesures conservatoires : isolement, suspension des comptes touchés | Référent sécurité |
| T+1 h | Cellule de crise réunie. **Préservation des journaux avant toute remise en service** | Direction |
| T+4 h | Périmètre estimé : quelles données, combien de personnes | Équipe technique |
| T+24 h | Correction déployée, service rétabli | Équipe technique |
| **T+72 h** | **Notification CNIL** — délai légal impératif | DPO |
| T+72 h | Information individuelle des personnes si le risque est élevé | Communication |
| T+7 j | Rapport d'incident : chronologie, cause racine, mesures | Référent sécurité |
| T+30 j | Retour d'expérience, plan d'action, mise à jour de la matrice de risques | Ensemble |

**Point critique** : le délai de 72 heures court à partir de la **prise de connaissance**, pas de la fin de l'analyse. Une notification partielle dans les temps vaut mieux qu'une notification complète hors délai — le RGPD prévoit explicitement la notification par étapes.

### Conditions de remontée et de descente

**Remontée** — Confirmation d'un accès non autorisé, découverte d'une donnée personnelle hors du système, incident dépassant le délai de correction contractuel, ou sollicitation médiatique.

**Descente** — Service rétabli et vérifié, cause racine identifiée et corrigée, absence de persistance de l'attaquant confirmée, correctif couvert par un test de non-régression. La descente est prononcée par le niveau qui a déclenché l'escalade, jamais par un niveau inférieur.

### Communication de crise

| Public | Canal | Contenu | Quand |
|---|---|---|---|
| Interne | Ticket + réunion de crise | Tout, sans filtre | Immédiat |
| Ministère | Point de contact désigné | Nature, périmètre, mesures, délai | Sous 1 h |
| Personnes concernées | Courriel | Fait, données touchées, mesures prises, recommandations | Sous 72 h si risque élevé |
| CNIL | Téléservice de notification | Formulaire réglementaire | Sous 72 h |
| Public | Communiqué | Faits établis uniquement | Sur décision de la direction |

**Règle de communication** : ne jamais annoncer un périmètre non vérifié. Une estimation revue à la hausse trois jours plus tard fait plus de dégâts que le silence initial.

## Bonnes pratiques de développement

**Revue systématique** — Aucune fusion sans relecture. Le modèle de demande de fusion comporte une section dédiée à l'impact sur les données personnelles, à remplir pour chaque changement.

**Sécurité vérifiée par la chaîne d'intégration** — Audit des dépendances, détection de secrets versionnés, tests de contrôle d'accès. Ces contrôles sont bloquants : ils ne dépendent pas de la vigilance d'un relecteur.

**Tout correctif de sécurité est accompagné d'un test** — Le test échoue avant le correctif et réussit après. C'est ce qui transforme une correction ponctuelle en garantie permanente, et interdit la réapparition silencieuse d'une faille.

**Aucun secret dans le code** — Vérifié automatiquement, dans les deux sens : le contrôle laisse passer les gabarits et détecte une valeur réelle.

**Validation des entrées côté serveur** — Les contraintes portent sur l'entité, donc s'appliquent quel que soit le point d'entrée : formulaire, API ou commande.

**Moindre privilège** — Le conteneur applicatif de production tourne sous `www-data`, jamais `root`. La base n'expose aucun port. Le compte MySQL applicatif n'a pas les droits d'administration.

**Journalisation utile** — Le canal `securite` trace les connexions, créations de comptes, actions d'administration et blocages, sans jamais consigner de mot de passe ni de donnée de santé. Un journal qui contiendrait des données sensibles déplacerait le problème au lieu de le résoudre.

## Contrôles à réaliser avant la mise en production

- [ ] Analyse d'impact conduite et validée avec le DPO
- [ ] Certificat HTTPS installé, redirection HTTP vers HTTPS active
- [ ] En-tête `Strict-Transport-Security` activé dans `docker/nginx/default.conf`
- [ ] Chiffrement au repos du volume de base de données
- [ ] Sauvegarde automatique planifiée **et restauration testée**
- [ ] Supervision et alertes branchées sur le canal `securite`
- [ ] Comptes de démonstration supprimés — ce sont des identifiants publics
- [ ] Test d'intrusion sur les modules Comptes utilisateurs et API
- [ ] Politique de sécurité de contenu (CSP) définie
- [ ] Double facteur activé sur les comptes administrateurs
