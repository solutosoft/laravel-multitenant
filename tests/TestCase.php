<?php

namespace SolutoSoft\MultiTenant\Tests;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Events\Dispatcher;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $db = new Manager();

        $db->addConnection([
            'driver'    => 'sqlite',
            'database'  => ':memory:',
        ]);

        $db->bootEloquent();
        $db->setAsGlobal();

        $this->createSchema();

        $this->seedData();

        Model::setEventDispatcher(new Dispatcher());
        Model::clearBootedModels();
    }

    /**
     * Get a database connection instance.
     *
     * @return \Illuminate\Database\Connection
     */
    protected function getConnection()
    {
        return Model::getConnectionResolver()->connection();
    }

    /**
     * Get a schema builder instance.
     *
     * @return \Illuminate\Database\Schema\Builder
     */
    protected function getSchemaBuilder()
    {
        return $this->getConnection()->getSchemaBuilder();
    }

    /**
     * Setup the database schema.
     *
     * @return void
     */
    protected function createSchema()
    {
        $this->getSchemaBuilder()->create('people', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstName');
            $table->string('lastName');
            $table->string('login')->nullable();
            $table->string('password')->nullable();
            $table->string('remember_token')->nullable();
            $table->string('active');
            $table->integer('tenant_id')->unsigned()->nullable(true);
        });
    }

    /**
     * Seeds the database.
     *
     * @return void
     */
    protected function seedData()
    {
        $this->getConnection()->table('people')->insert([
            [
                'id' => 1,
                'firstName' => 'Admin',
                'lastName' => 'Administrator',
                'tenant_id' => null,
                'login' => 'admin',
                'password' => 'admin',
                'active' => true,
                'remember_token' => 'token-admin'
            ],[
                'id' => 2,
                'firstName' => 'Tenant1',
                'lastName' => 'Tenant1 Last Name',
                'login' => 'tenant1',
                'password' => 'tenant1',
                'remember_token' => 'token-tenant1',
                'active' => false,
                'tenant_id' => 1,
            ],[
                'id' => 3,
                'firstName' => 'Tenant2',
                'lastName' => 'Tenant2 Last Name',
                'login' => 'tenant2',
                'password' => 'tenant2',
                'remember_token' => 'token-tenant2',
                'active' => true,
                'tenant_id' => 1
            ],[
                'id' => 4,
                'firstName' => 'SubTenant1',
                'lastName' => 'SubTenant1 Last Name',
                'login' => 'subtenant',
                'password' => 'subtenant',
                'remember_token' => 'token-subtenant',
                'active' => true,
                'tenant_id' => 2
            ]
        ]);
    }
}
