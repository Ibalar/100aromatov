<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        // Hard-force test environment before the app is bootstrapped.
        putenv('APP_ENV=testing');
        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DATABASE=database/testing.sqlite');

        $_ENV['APP_ENV'] = 'testing';
        $_ENV['DB_CONNECTION'] = 'sqlite';
        $_ENV['DB_DATABASE'] = 'database/testing.sqlite';
        $_SERVER['APP_ENV'] = 'testing';
        $_SERVER['DB_CONNECTION'] = 'sqlite';
        $_SERVER['DB_DATABASE'] = 'database/testing.sqlite';

        parent::setUp();

        // Safety net: never allow tests to run against non-sqlite DB.
        $connection = (string) config('database.default');
        $database = (string) config('database.connections.' . $connection . '.database');

        if ($connection !== 'sqlite' || !str_contains(str_replace('\\', '/', $database), 'database/testing.sqlite')) {
            throw new RuntimeException(
                sprintf(
                    'Unsafe test database configuration detected: connection=%s, database=%s',
                    $connection,
                    $database
                )
            );
        }
    }
}
