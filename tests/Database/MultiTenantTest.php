<?php

namespace Soluto\MultiTenant\Test\Database;

use Soluto\MultiTenant\Test\TestCase;
use Soluto\MultiTenant\Test\Models\Person;
use Illuminate\Support\Facades\Facade;

class MultiTenantTest extends TestCase
{
    public function testDataWithValidUser()
    {
        $admin = Person::withoutTenant()->findOrFail(1);

        $mock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['guest', 'user'])
            ->getMock();

        $mock->method('user')
             ->willReturn($admin);

        $mock->method('guest')
             ->willReturn(false);

        $app = new ApplicationStub();
        $app->setAttributes(['auth' => $mock]);

        Facade::setFacadeApplication($app);

        $test = Person::create([
            'firstName' => 'Test',
            'lastName' => 'Test Last Name',
        ]);

        $forced = Person::forceCreate([
            'firstName' => 'Forced',
            'lastName' => 'Forced Last Name',
            'tenant_id' => 2
        ]);

        $forced = Person::find($forced->id);
        $test = Person::find($test->id);

        $this->assertEquals(1, $test->tenant_id);
        $this->assertNull($forced);

        $this->assertEquals(3, Person::count());
        $this->assertEquals(6, Person::withoutTenant()->count());
    }

    /**
     * @expectedException \Soluto\MultiTenant\Database\TenantException
     */
    public function testLoadDataWithGuestUser()
    {
        Person::all();
    }

    /**
     * @expectedException \Soluto\MultiTenant\Database\TenantException
     */
    public function testSaveDataWithGuestUser()
    {
        $person = Person::forceCreate([
            'firstName' => 'Valid',
            'lastName' => 'With Guest User',
            'tenant_id' => 1
        ]);

        $this->assertNotNull($person);

        Person::create([
            'firstName' => 'Exception',
            'lastName' => 'People'
        ]);
    }
}

class ApplicationStub implements \ArrayAccess
{
    protected $attributes = [];

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function instance($key, $instance)
    {
        $this->attributes[$key] = $instance;
    }

    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetGet($key)
    {
        return $this->attributes[$key];
    }

    public function offsetSet($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function offsetUnset($key)
    {
        unset($this->attributes[$key]);
    }
}