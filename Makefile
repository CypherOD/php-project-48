install:
	composer install

validate:
	@vendor/bin/phpcs --standard=PSR12 src

test:
	composer exec --verbose phpunit tests -- --testdox

test-coverage:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-clover=build/coverage/clover.xml

test-coverage-text:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-text

lint:
	./vendor/bin/phpcs

link:
	sudo ln -sf $(PWD)/bin/gendiff /usr/local/bin/gendiff

link-wsl:
	sudo ln -sf $(PWD)/bin/gendiff /usr/local/bin/gendiff

unlink:
	sudo rm -f /usr/local/bin/gendiff

unlink-wsl:
	sudo rm -f /usr/local/bin/gendiff