#!/bin/bash
`which php` `dirname $0`/../vendor/phpunit/phpunit/phpunit --configuration `dirname $0`/phpunit.xml
