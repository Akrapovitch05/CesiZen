-- ---------------------------------------------------------------------------
-- Donnees de demonstration CESIZen
--
-- A charger uniquement dans un environnement de developpement, de test ou de
-- production simulee. NE JAMAIS EXECUTER SUR UNE PRODUCTION REELLE : ce
-- fichier ecrase les tables et cree des comptes dont les mots de passe sont
-- publics, puisqu ils figurent dans le depot.
--
-- L image de production est construite sans le bundle de fixtures Doctrine,
-- qui est une dependance de developpement. Ce fichier SQL est donc le moyen
-- d alimenter un environnement de demonstration bati sur l image reelle.
--
-- Chargement :
--   docker compose --env-file .env.local -f compose.yaml -f compose.prod.yaml \
--     -p cesizen-prod exec -T database \
--     mysql -ucesizen -p<mot_de_passe> cesizen < docker/donnees-demonstration.sql
--
-- Comptes crees :
--   admin@cesizen.fr  / Admin1!Cesizen   (ROLE_ADMIN)
--   marie@cesizen.fr  / User1!Cesizen    (ROLE_USER)
-- ---------------------------------------------------------------------------

-- Remise a zero, pour que le fichier puisse etre rejoue sans erreur de doublon.
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM `utilisateur_activite`;
DELETE FROM `seance_exercice`;
DELETE FROM `activite_exercice`;
DELETE FROM `utilisateur`;
DELETE FROM `seance`;
DELETE FROM `exercice`;
DELETE FROM `activite`;
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO `activite` (`id`, `nom`, `description`, `duree`, `url_media`) VALUES (1,'Yoga doux','Seance de yoga accessible a tous, centree sur la respiration et les etirements.',30,'https://www.youtube.com/watch?v=OG5Iu-SLno4');
INSERT INTO `activite` (`id`, `nom`, `description`, `duree`, `url_media`) VALUES (2,'Meditation guidee','Dix minutes de meditation guidee pour relacher les tensions.',10,'https://www.youtube.com/watch?v=inpok4MKVLM');
INSERT INTO `activite` (`id`, `nom`, `description`, `duree`, `url_media`) VALUES (3,'Marche consciente','Marche lente en pleine conscience, en exterieur de preference.',20,'https://www.youtube.com/watch?v=BjkPuChM8-4');
INSERT INTO `activite` (`id`, `nom`, `description`, `duree`, `url_media`) VALUES (4,'Etirements du soir','Sequence d\'etirements pour preparer le corps au sommeil.',15,'https://www.youtube.com/watch?v=6E5NKZ2Bw_A');
INSERT INTO `exercice` (`id`, `nom`, `duree_inspiration`, `duree_apnee`, `duree_expiration`, `description`) VALUES (1,'7-4-8',7,4,8,'Inspiration 7 s, apnee 4 s, expiration 8 s. Rythme apaisant, adapte a l\'endormissement.');
INSERT INTO `exercice` (`id`, `nom`, `duree_inspiration`, `duree_apnee`, `duree_expiration`, `description`) VALUES (2,'5-5',5,0,5,'Inspiration 5 s, expiration 5 s. Rythme de reference de la coherence cardiaque.');
INSERT INTO `exercice` (`id`, `nom`, `duree_inspiration`, `duree_apnee`, `duree_expiration`, `description`) VALUES (3,'4-6',4,0,6,'Inspiration 4 s, expiration 6 s. Expiration allongee, favorise la detente.');
INSERT INTO `seance` (`id`, `date_realisation`) VALUES (1,'2026-09-01 11:37:40');
INSERT INTO `utilisateur` (`id`, `id_seance`, `nom`, `prenom`, `email`, `password`, `date_inscription`, `roles`) VALUES (1,1,'Kibout','Akram','admin@cesizen.fr','$2y$13$MQKpPiG1YK/U3WxD50HE5OjX1Xw4NFozvVPKt1peMF7N1JRSTqa7.','2026-09-01','[\"ROLE_ADMIN\"]');
INSERT INTO `utilisateur` (`id`, `id_seance`, `nom`, `prenom`, `email`, `password`, `date_inscription`, `roles`) VALUES (2,1,'Dupont','Marie','marie@cesizen.fr','$2y$13$XrmDwZrduYvig2CiL2kTEu4CNqA9gyRR9ij7Y6dEbeJRnXzidcMnm','2026-09-01','[\"ROLE_USER\"]');
INSERT INTO `activite_exercice` (`activite_id`, `exercice_id`) VALUES (1,2);
INSERT INTO `activite_exercice` (`activite_id`, `exercice_id`) VALUES (4,1);
INSERT INTO `seance_exercice` (`seance_id`, `exercice_id`) VALUES (1,2);
INSERT INTO `utilisateur_activite` (`utilisateur_id`, `activite_id`) VALUES (1,1);
INSERT INTO `utilisateur_activite` (`utilisateur_id`, `activite_id`) VALUES (2,1);
