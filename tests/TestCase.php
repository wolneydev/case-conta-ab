<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        putenv('DB_DATABASE=challenge_test');
        $_ENV['DB_DATABASE'] = 'challenge_test';
        $_SERVER['DB_DATABASE'] = 'challenge_test';

        parent::setUp();
    }
}
