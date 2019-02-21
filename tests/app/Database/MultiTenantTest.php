<?php

namespace Soluto\MultiTenant\Tests\Database;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Soluto\MultiTenant\Tests\TestCase;
use Soluto\MultiTenant\Tests\Models\Person;

class MultiTenantTest extends TestCase
{
    use DatabaseMigrations;

    public function testDataWithValidUser()
    {
        $this->artisan('db:seed', ['--class' => 'PeopleSeeder']);

        /* @var $admin Illuminate\Contracts\Auth\Authenticatable */
        $admin = Person::withoutTenant()->findOrFail(1);
        $this->actingAs($admin);

        $test = Person::create([
            'firstName' => 'Test',
            'lastName' => 'Test Last Name',
            'login' => 'test',
            'password' => '123456',
            'super' => false,
        ]);

        $forced = Person::forceCreate([
            'firstName' => 'Forced',
            'lastName' => 'Forced Last Name',
            'login' => 'forced',
            'password' => 'forced',
            'super' => false,
            'tenant_id' => 2
        ]);

        $forced = Person::find($forced->id);
        $test = Person::find($test->id);

        $this->assertEquals(1, $test->tenant_id);
        $this->assertNull($forced);

        $this->assertEquals(3, Person::count());
        $this->assertEquals(6, Person::withoutTenant()->count());
    }

    public function testLoadDataWithGuestUser()
    {
        $this->setExpectedException('Soluto\MultiTenant\Database\TenantException');

        Person::all();
    }

    public function testSaveDataWithGuestUser()
    {
        $person = Person::forceCreate([
            'firstName' => 'Valid',
            'lastName' => 'With Guest User',
            'login' => 'guest',
            'password' => 'guest',
            'super' => false,
            'tenant_id' => 1
        ]);

        $this->assertNotNull($person);

        $this->setExpectedException('Soluto\MultiTenant\Database\TenantException');

        Person::create([
            'firstName' => 'Exception',
            'lastName' => 'People',
            'login' => 'except',
            'password' => 'invalid'
        ]);
    }


}
