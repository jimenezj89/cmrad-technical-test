PROJECT_NAME = cmrad

build: start ## Build project environment
	docker exec -ti $(PROJECT_NAME)_php composer install

start: ## Starts project environment
	cd docker && docker-compose -p $(PROJECT_NAME) up -d

stop: ## Stops project environment
	cd docker && docker-compose -p $(PROJECT_NAME) stop

clean: ## Remove all containers and images used in this project environment
	cd docker && docker-compose -p $(PROJECT_NAME) down -v --rmi all --remove-orphans

tests: ## Executes the complete test suite
	docker exec -ti $(PROJECT_NAME)_php vendor/bin/phpunit

tests_coverage: ## Generates test coverage report
	docker exec -ti $(PROJECT_NAME)_php vendor/bin/phpunit --coverage-html test_coverage_report

help: ## Shows this help
	@IFS=$$'\n' ; \
	help_lines=(`fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##/:/'`); \
	printf "%-30s %s\n" "target" "help" ; \
	printf "%-30s %s\n" "------" "----" ; \
	for help_line in $${help_lines[@]}; do \
		IFS=$$':' ; \
		help_split=($$help_line) ; \
		help_command=`echo $${help_split[0]} | sed -e 's/^ *//' -e 's/ *$$//'` ; \
		help_info=`echo $${help_split[2]} | sed -e 's/^ *//' -e 's/ *$$//'` ; \
		printf '\033[36m'; \
		printf "%-30s %s" $$help_command ; \
		printf '\033[0m'; \
		printf "%s\n" $$help_info; \
	done

.PHONY: build clean start stop tests help
