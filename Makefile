test:
	composer exec --verbose phpunit tests

test-coverage:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-html=build/coverage
	xdg-open build/coverage/index.html

test-coverage-text:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-text

lint:
	./vendor/bin/phpcs . --standard=PSR12 --ignore=vendor/