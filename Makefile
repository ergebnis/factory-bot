.PHONY: it
it: refactoring coding-standards security-analysis static-code-analysis tests tests-example ## Runs the refactoring, coding-standards, security-analysis, static-code-analysis, tests, and tests-example targets

.PHONY: code-coverage
code-coverage: vendor ## Collects coverage from running unit and integration tests with phpunit/phpunit
	vendor/bin/phpunit --configuration=test/phpunit.xml --coverage-text --testsuite=unit,integration

.PHONY: coding-standards
coding-standards: vendor ## Lints YAML files with yamllint, normalizes composer.json with ergebnis/composer-normalize, and fixes code style issues with friendsofphp/php-cs-fixer
	yamllint -c .yamllint.yaml --strict .
	composer normalize
	vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php --diff --show-progress=dots --verbose
	vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.fixture.php --diff --show-progress=dots --verbose

.PHONY: dependency-analysis
dependency-analysis: phive vendor ## Runs a dependency analysis with maglnet/composer-require-checker
	.phive/composer-require-checker check --config-file=$(shell pwd)/composer-require-checker.json --verbose

.PHONY: doctrine
doctrine: vendor ## Shows and validates Docrine mapping information
	bin/console orm:info
	bin/console orm:validate-schema --skip-sync

.PHONY: help
help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: phive
phive: .phive ## Installs dependencies with phive
	PHIVE_HOME=.build/phive phive install --trust-gpg-keys 0x033E5F8D801A2F8D

.PHONY: refactoring
refactoring: vendor ## Runs automated refactoring with rector/rector
	vendor/bin/rector process --config=rector.php

.PHONY: security-analysis
security-analysis: vendor ## Runs a security analysis with composer
	composer audit

.PHONY: static-code-analysis
static-code-analysis: vendor ## Runs a static code analysis with phpstan/phpstan
	vendor/bin/phpstan clear-result-cache --configuration=phpstan.neon
	vendor/bin/phpstan --configuration=phpstan.neon --memory-limit=-1

.PHONY: static-code-analysis-baseline
static-code-analysis-baseline: vendor ## Generates a baseline for static code analysis with phpstan/phpstan
	vendor/bin/phpstan clear-result-cache --configuration=phpstan.neon
	vendor/bin/phpstan --allow-empty-baseline --configuration=phpstan.neon --generate-baseline=phpstan-baseline.neon --memory-limit=-1

.PHONY: tests
tests: vendor ## Runs unit and integration tests with phpunit/phpunit
	vendor/bin/phpunit --configuration=test/phpunit.xml --testsuite=unit
	vendor/bin/phpunit --configuration=test/phpunit.xml --testsuite=integration

.PHONY: tests-example
tests-example: vendor ## Runs auto-review and unit tests for examples with phpunit/phpunit
	vendor/bin/phpunit --configuration=example/test/AutoReview/phpunit.xml
	vendor/bin/phpunit --configuration=example/test/Unit/phpunit.xml

vendor: composer.json composer.lock
	composer validate --strict
	composer install --no-interaction --no-progress
