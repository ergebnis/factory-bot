.PHONY: it
it: coding-standards static-code-analysis tests ## Runs the coding-standards, static-code-analysis, and tests targets

.PHONY: code-coverage
code-coverage: vendor ## Collects coverage from running unit tests with phpunit/phpunit
	mkdir -p .build/phpunit
	vendor/bin/phpunit --configuration=test/phpunit.xml --coverage-text --testsuite=unit,integration

.PHONY: coding-standards
coding-standards: vendor ## Normalizes composer.json with ergebnis/composer-normalize, lints YAML files with yamllint and fixes code style issues with friendsofphp/php-cs-fixer
	composer normalize
	yamllint -c .yamllint.yaml --strict .
	mkdir -p .build/php-cs-fixer
	vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php --diff --diff-format=udiff --verbose
	vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.fixture.php --diff --diff-format=udiff --verbose

.PHONY: dependency-analysis
dependency-analysis: vendor ## Runs a dependency analysis with maglnet/composer-require-checker
	.phive/composer-require-checker check --config-file=$(shell pwd)/composer-require-checker.json

.PHONY: doctrine
doctrine: vendor ## Shows and validates Docrine mapping information
	vendor/bin/doctrine orm:info
	vendor/bin/doctrine orm:validate-schema --skip-sync

.PHONY: help
help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: static-code-analysis
static-code-analysis: vendor ## Runs a static code analysis with phpstan/phpstan and vimeo/psalm
	mkdir -p .build/phpstan
	vendor/bin/phpstan analyse --configuration=phpstan.neon --memory-limit=-1
	mkdir -p .build/psalm
	vendor/bin/psalm --config=psalm.xml --diff --show-info=false --stats --threads=4

.PHONY: static-code-analysis-baseline
static-code-analysis-baseline: vendor ## Generates a baseline for static code analysis with phpstan/phpstan and vimeo/psalm
	mkdir -p .build/phpstan
	vendor/bin/phpstan analyze --configuration=phpstan.neon --generate-baseline=phpstan-baseline.neon  --memory-limit=-1
	mkdir -p .build/psalm
	vendor/bin/psalm --config=psalm.xml --set-baseline=psalm-baseline.xml

.PHONY: tests
tests: vendor ## Runs auto-review, unit, and integration tests with phpunit/phpunit
	mkdir -p .build/phpunit
	vendor/bin/phpunit --configuration=test/phpunit.xml --testsuite=auto-review
	vendor/bin/phpunit --configuration=test/phpunit.xml --testsuite=unit
	vendor/bin/phpunit --configuration=test/phpunit.xml --testsuite=integration

.PHONY: tests-example
tests-example: vendor ## Runs auto-review and unit tests for examples with phpunit/phpunit
	mkdir -p .build/phpunit
	vendor/bin/phpunit --configuration=example/test/AutoReview/phpunit.xml
	vendor/bin/phpunit --configuration=example/test/Unit/phpunit.xml

vendor: composer.json composer.lock
	composer validate --strict
	composer install --no-interaction --no-progress
