<?php

namespace Soluto\MultiTenant\Tests;


abstract class TestCase extends \Illuminate\Foundation\Testing\TestCase
{

    /**
     * @inheritdoc
     */
    public function createApplication()
    {
        return require __DIR__ . '/bootstrap/app.php';
    }
}
