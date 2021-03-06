<?php

/**
 * Find the auto loader file
 */
$files = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
    __DIR__ . '/../../../vendor/autoload.php',
    __DIR__ . '/../../../../vendor/autoload.php',

];

foreach ($files as $file) {
    if (file_exists($file)) {
        $loader = require_once $file;
        $loader->addPsr4('PgAsync\\Tests\\', __DIR__);
        break;
    }
}

\PgAsync\Tests\TestCase::setDbUser(getenv("USER"));
if (getenv("TRAVIS") == "true") {
    \PgAsync\Tests\TestCase::setDbUser("postgres");
}

// cleanup remnants if there are any
exec("dropdb --if-exists " . \PgAsync\Tests\TestCase::getDbName() . " -U '" . \PgAsync\Tests\TestCase::getDbUser() . "'");

// Create the Test database
exec("psql -c 'create database " . \PgAsync\Tests\TestCase::getDbName() . ";' -U '" . \PgAsync\Tests\TestCase::getDbUser() . "'");

exec("psql -f " . __DIR__ . "/test_db.sql " . \PgAsync\Tests\TestCase::getDbName() . " " . \PgAsync\Tests\TestCase::getDbUser());

register_shutdown_function(function () {
    exec("dropdb --if-exists " . \PgAsync\Tests\TestCase::getDbName() . " -U '" . \PgAsync\Tests\TestCase::getDbUser() . "'");
});
