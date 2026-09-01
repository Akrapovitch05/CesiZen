# ---------------------------------------------------------------------------
# Raccourcis de pilotage des environnements CESIZen.
# `make aide` liste toutes les cibles disponibles.
# ---------------------------------------------------------------------------
SHELL := /bin/bash
ENVFILE := --env-file .env.local
DEV  := docker compose $(ENVFILE)
TEST := docker compose $(ENVFILE) -f compose.yaml -f compose.test.yaml -p cesizen-test
PROD := docker compose $(ENVFILE) -f compose.yaml -f compose.prod.yaml -p cesizen-prod

.DEFAULT_GOAL := aide
.PHONY: aide dev-up dev-down dev-logs dev-shell dev-migrate dev-fixtures test test-down prod-up prod-down prod-logs lint audit ci

aide: ## Affiche cette aide
	@grep -E "^[a-zA-Z_-]+:.*?## .*$$" $(MAKEFILE_LIST) | awk -F":.*?## " "{printf \"  \033[36m%-16s\033[0m %s\n\", \$$1, \$$2}"

# --------------------------- DÉVELOPPEMENT ---------------------------------
dev-up: ## Démarre l environnement de dev (http://localhost:8080, mails sur :8025)
	$(DEV) up -d --build

dev-down: ## Arrête l environnement de dev
	$(DEV) down

dev-logs: ## Suit les logs de dev
	$(DEV) logs -f

dev-shell: ## Ouvre un shell dans le conteneur PHP de dev
	$(DEV) exec php sh

dev-migrate: ## Applique les migrations Doctrine en dev
	$(DEV) exec php php bin/console doctrine:migrations:migrate --no-interaction

dev-fixtures: ## Recharge les jeux de données de démonstration
	$(DEV) exec php php bin/console doctrine:fixtures:load --no-interaction

# ------------------------------- TESTS -------------------------------------
test: ## Lance la suite de tests dans un environnement jetable
	$(TEST) up -d --build
	$(TEST) exec -T php php bin/console doctrine:migrations:migrate --no-interaction --env=test
	$(TEST) exec -T php vendor/bin/phpunit

test-down: ## Détruit l environnement de test
	$(TEST) down -v

# ---------------------------- PRODUCTION -----------------------------------
prod-up: ## Démarre la production simulée (http://localhost:8081)
	$(PROD) up -d --build

prod-down: ## Arrête la production simulée
	$(PROD) down

prod-logs: ## Suit les logs de production
	$(PROD) logs -f

# ------------------------------ QUALITÉ ------------------------------------
lint: ## Vérifie la syntaxe des conteneurs, templates Twig et fichiers YAML
	$(DEV) exec -T php php bin/console lint:container
	$(DEV) exec -T php php bin/console lint:twig templates
	$(DEV) exec -T php php bin/console lint:yaml config --parse-tags

audit: ## Recherche des vulnérabilités connues dans les dépendances
	$(DEV) exec -T php composer audit

ci: lint audit test ## Reproduit localement la chaîne d intégration continue
