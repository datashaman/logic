test: phpcheck-no-defects phpunit

clean:
	rm -rf build/

watch:
	while inotifywait -e close_write -r composer.* ./src ./checks ./functions ./tests; do make phpunit phpcheck; done

watch-playground:
	while inotifywait -e close_write -r composer.* ./src ./checks ./functions ./playground ./tests; do php playground/test.php; done

classes:
	mkdir -p resources/json/
	./generate-classes > resources/json/classes.json

classes-gists:
	mkdir -p resources/json/
	./generate-classes --gists > resources/json/classes.json

functions:
	mkdir -p resources/json/
	./generate-functions > resources/json/functions.json

functions-gists:
	mkdir -p resources/json/
	./generate-functions --gists > resources/json/functions.json

namespaces:
	mkdir -p resources/json/
	./generate-namespaces 'Datashaman\\Logic' > resources/json/namespaces.json

namespaces-gists:
	mkdir -p resources/json/
	./generate-namespaces --gists 'Datashaman\\Logic' > resources/json/namespaces.json

phpcheck:
	@phpcheck

phpcheck-coverage:
	sudo phpenmod xdebug
	@phpcheck --coverage-html report/phpcheck-coverage
	sudo phpdismod xdebug
	@xdg-open report/phpcheck-coverage/index.html

phpcheck-log-junit:
	@phpcheck --log-junit build/phpcheck.xml

phpcheck-log-text:
	@phpcheck --log-text build/phpcheck.txt

phpcheck-no-defects:
	@phpcheck -d

php-cs-fixer:
	php-cs-fixer fix --using-cache=no

phpmd:
	@phpmd src,checks text phpmd.xml

phpstan:
	@phpstan analyse src checks --level 5

phpunit:
	@phpunit

phpunit-coverage:
	sudo phpenmod xdebug
	@phpunit --coverage-html report/phpunit-coverage
	sudo phpdismod xdebug
	@xdg-open report/phpunit-coverage/index.html

profile:
	sudo phpenmod xdebug
	# @php -d xdebug.profiler_enable=1 `which phpunit`
	@php -d xdebug.profiler_enable=1 `which phpcheck`
	sudo phpdismod xdebug

deploy:
	ssh $(DEPLOY_HOST) "cd logic && git pull && yarn && node_modules/.bin/webpack --mode=production && mv current previous && mv build current"

webpack-dev-server:
	webpack-dev-server

webpack-development: clean
	webpack --mode=development

webpack-production: clean
	webpack --mode=production

webpack-watch:
	webpack --mode=development --watch
