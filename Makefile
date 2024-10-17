# vim: set tabstop=8 softtabstop=8 noexpandtab:
.PHONY: static-code-analysis
static-code-analysis:
	symfony php vendor/bin/phpstan analyse --configuration phpstan.neon.dist --no-progress

.PHONY: static-code-analysis-baseline
static-code-analysis-baseline:
	symfony php vendor/bin/phpstan analyse --configuration phpstan.neon.dist --generate-baseline=phpstan-baseline.neon --no-progress

.PHONY: cs
cs:
	symfony php vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --diff --verbose

.PHONY: tests
tests:
	php vendor/bin/phpunit -v
