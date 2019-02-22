<?php

namespace Soluto\MultiTenant\Test\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Facades\Hash;
use Soluto\MultiTenant\Database\MultiTenant;
use Soluto\MultiTenant\Database\Tenant;

/**
 * @property integer id
 * @property string name
 * @property string login
 * @property \Carbon\Carbon birthDate
 * @property string password
 * @property string token
 * @property boolean super
 * @property integer tenant_id
 */
class Person extends Model implements Tenant
{
    use MultiTenant;

    protected $table = 'people';

    protected $fillable = [
        'firstName',
        'lastName',
    ];

    public $timestamps = false;

    /**
     * @inheritdoc
     */
    public function getTenantId()
    {
        return ($this->tenant_id) ? $this->tenant_id : $this->id;
    }

    /**
     * @inheritdoc
     */
    public function isRoot()
    {
        return $this->id === 1;
    }

}
