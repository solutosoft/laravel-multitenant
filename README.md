# Laravel Multi Tenancy

[![Build Status](https://github.com/solutosoft/laravel-multitenant/actions/workflows/tests.yml/badge.svg)](https://github.com/solutosoft/laravel-multitenant/actions)
[![Total Downloads](https://poser.pugx.org/solutosoft/laravel-multitenant/downloads.png)](https://packagist.org/packages/solutosoft/multitenant)
[![Latest Stable Version](https://poser.pugx.org/solutosoft/laravel-multitenant/v/stable.png)](https://packagist.org/packages/solutosoft/multitenant)

The **Shared Database** used by all tenants, means that we keep data from all the tenants in the same database. To isolate tenant specific data, we will have to add a discriminator column like `tenant_id` to every table which is tenant specific, and to make sure that all the queries and commands will filter the data based on it.

With this strategy dealing with tenant shared data is simple, we just don't filter it. Isolating data is what we need to deal with. For this we need to make sure that ALL the queries and the commands that deal with tenant specific data get filtered by the `tenant_id`.

This extension allows control the Eloquent with shared database used by all tenants.

Installation
------------

The preferred way to install this library is through composer.

Either run

`composer require --prefer-dist solutosoft/laravel-multitenant "*"`

or add

`"solutosoft/laravel-multitenant": "*"`

to the require section of your composer.json.

Usage
-----

1. Create table with `tenant_id` column:

```php
/**
 * Run the migrations.
 *
 * @return void
  */
public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->increments('id');
        $table->string('firstName');
        $table->string('lastName');
        $table->string('login')->nullable();
        $table->string('password')->nullable();
        $table->string('remember_token')->nullable();
        $table->string('active');
        $table->integer('tenant_id')->nullable(true);
    });

    $Schema::create('pets', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
        $table->integer('tenant_id');
    });
}
```

2. Uses the `MultiTenant` trait, it add a [Global Scope](https://laravel.com/docs/5.7/eloquent#global-scope) filtering
any query by `tenant_id` column.

```php

<?php

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Solutosoft\MultiTenant\MultiTenant;
use Solutosoft\MultiTenant\Tenant;

class User extends Model implements Tenant
{
    use MultiTenant, Authenticatable;

    /**
     * @inheritdoc
     */
    public function getTenantId()
    {
        return $this->tenant_id;
    }

    ...
}

class Pet extends Model
{

  use MultiTenant;

  ...

}

```

Now when you save or execute same query the `tenant_id` column will be used. Example:

```php
// It's necessary will be logged in

$users = App\User::where('active', 1)->get();
// select * from `users` where `active` = 1 and tenant_id = 1

$pet = Pet::create(['name' => 'Bob']);
// insert into `pet` (`name`, 'tenant_id') values ('Bob', 1)
```

Auth Service Provider
---------------------

It's necessary change the authentication service provider:

1. Creates new file: `app/Providers/TenantUserProvider.php`

```php

<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Solutosoft\MultiTenant\TenantScope;

class TenantUserProvider extends EloquentUserProvider
{

    protected function newModelQuery($model = null)
    {
        return parent::newModelQuery($model)->withoutGlobalScope(TenantScope::class);
    }

}
```

2. Edit `app/Providers/AuthServiceProvider.php`

```php

<?php

namespace App\Providers;

class AuthServiceProvider extends ServiceProvider
{
    ...

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        ...

        Auth::provider('multitenant', function($app, $config) {
            return new TenantUserProvider($app['hash'], $config['model']);
        });
    }
}
```

3. Edit `config/auth.php`

```php

return [
    ....
    'providers' => [
        'users' => [
            'driver' => 'multitenant',
            'model' => App\Models\User::class,
        ],
    ],

    ...
```









