<?php

namespace Solutosoft\MultiTenant\Tests;

use Solutosoft\MultiTenant\Tests\Models\Person;
use Illuminate\Support\Facades\Facade;

class MultiTenantTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $app = new ApplicationStub();
        Facade::setFacadeApplication($app);
    }

    public function testDataWithValidUser()
    {
        $admin = Person::withoutTenant()->findOrFail(1);

        $mock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['guest', 'user'])
            ->getMock();

        $mock->method('user')
             ->willReturn($admin);

        $mock->method('guest')
             ->willReturn(false);

        /** @var $app ApplicationStub */
        $app = Facade::getFacadeApplication();
        $app->setAttributes(['auth' => $mock]);

        $test = Person::create([
            'firstName' => 'Test',
            'lastName' => 'Test Last Name',
            'active' => true
        ]);

        $forced = Person::forceCreate([
            'firstName' => 'Forced',
            'lastName' => 'Forced Last Name',
            'active' => true,
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
     * @expectedException \RuntimeException
     */
    public function testLoadDataWithGuestUser()
    {
        Person::all();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSaveDataWithGuestUser()
    {
        $person = Person::forceCreate([
            'firstName' => 'Valid',
            'lastName' => 'With Guest User',
            'active' => true,
            'tenant_id' => 1
        ]);

        $this->assertNotNull($person);

        Person::create([
            'firstName' => 'Exception',
            'lastName' => 'People',
            'active' => true
        ]);
    }
}

class ApplicationStub implements \ArrayAccess
{

    public function __construct ()
    {
        $this->attributes = ['app' => $this];
    }

    public function setAttributes($attributes)
    {
        $this->attributes = array_merge($attributes, $this->attributes);
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

    public function runningInConsole()
    {
        return false;
    }
}
