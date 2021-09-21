<?php

namespace Solutosoft\MultiTenant\Tests;

use Solutosoft\MultiTenant\Tests\Models\Person;
use Illuminate\Support\Facades\Facade;
use RuntimeException;
use Solutosoft\MultiTenant\Tests\Models\Post;

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
        $this->mockAuth();

        $test = Person::create([
            'firstName' => 'Test',
            'lastName' => 'Test Last Name',
            'active' => true
        ]);

        $forced = Person::forceCreate([
            'firstName' => 'Forced',
            'lastName' => 'Forced Last Name',
            'active' => true,
            'organization_id' => 2
        ]);

        $forced = Person::find($forced->id);
        $test = Person::find($test->id);

        $this->assertEquals(1, $test->organization_id);
        $this->assertNull($forced);

        $this->assertEquals(3, Person::count());
        $this->assertEquals(6, Person::withoutTenant()->count());
    }

    public function testDisabledTenantScope()
    {
        $this->mockAuth();
        $this->assertEquals(2, Post::count());
    }

    public function testLoadDataWithGuestUser()
    {
        $this->expectException(RuntimeException::class);

        Person::all();
    }

    public function testSaveDataWithGuestUser()
    {
        $this->expectException(RuntimeException::class);

        $person = Person::forceCreate([
            'firstName' => 'Valid',
            'lastName' => 'With Guest User',
            'active' => true,
            'organization_id' => 1
        ]);

        $this->assertNotNull($person);

        Person::create([
            'firstName' => 'Exception',
            'lastName' => 'People',
            'active' => true
        ]);
    }

    private function mockAuth()
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
