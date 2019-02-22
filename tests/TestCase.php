<?php

namespace Soluto\MultiTenant\Test;

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
            ],[
                'id' => 2,
                'firstName' => 'Tenant1',
                'lastName' => 'Tenant1 Last Name',
                'tenant_id' => 1,
            ],[
                'id' => 3,
                'firstName' => 'Tenant2',
                'lastName' => 'Tenant2 Last Name',
                'tenant_id' => 1
            ],[
                'id' => 4,
                'firstName' => 'SubTenant1',
                'lastName' => 'SubTenant1 Last Name',
                'tenant_id' => 2
            ]
        ]);
    }
}
