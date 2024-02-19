
help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

build: ## build docker stack
	docker-compose build

up: ## up docker stack
	docker-compose up -d

down: ## down docker stack
	docker-compose down

start: ## start server
	symfony server:start --port=8000 -d

stop: ## stop server
	symfony server:stop

status: ## server status
	symfony server:status

migration: ## run migrations
	symfony console doctrine:schema:create
