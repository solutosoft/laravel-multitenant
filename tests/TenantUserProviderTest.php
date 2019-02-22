<?php

namespace Soluto\MultiTenant\Test;

use Soluto\MultiTenant\Test\TestCase;
use Soluto\MultiTenant\Test\Models\Person;
use Soluto\MultiTenant\Providers\TenantUserProvider;
use Illuminate\Contracts\Hashing\Hasher;

class TenantUserProviderTest extends TestCase
{
    public function testRetrieveMethods()
    {
        /* @var \Illuminate\Contracts\Hashing\Hasher $hasher */
        $hasher = $this->getMockBuilder(Hasher::class)
            ->setMethods(['check', 'make', 'info', 'needsRehash'])
            ->getMock();

        $hasher->method('check')->willReturn(true);

        $provider = new TenantUserProvider($hasher, Person::class);

        $user = $provider->retrieveById(2);
        $this->assertNotNull($user);

        $user = $provider->retrieveByToken(3, 'token-tenant2');
        $this->assertNotNull($user);

        $user = $provider->retrieveByCredentials([
            'login' => 'subtenant',
            'password' => 'subtenant'
        ]);
        $this->assertNotNull($user);
    }

    public function testScope()
    {
        /* @var \Illuminate\Contracts\Hashing\Hasher $hasher */
        $hasher = $this->getMockBuilder(Hasher::class)
            ->setMethods(['check', 'make', 'info', 'needsRehash'])
            ->getMock();

        $hasher->method('check')->willReturn(true);

        $provider = new TenantUserProvider($hasher, Person::class, 'active');

        $user = $provider->retrieveById(2);
        $this->assertNull($user);

        $user = $provider->retrieveByToken(2, 'token-tenant1');
        $this->assertNull($user);

        $user = $provider->retrieveByCredentials([
            'login' => 'tenant1',
            'password' => 'tenant1'
        ]);
        $this->assertNull($user);
    }
}