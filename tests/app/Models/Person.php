<?php

namespace Soluto\MultiTenant\Tests\Models;

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
class Person extends Model implements
    Tenant,
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, MultiTenant;

    public $timestamps = false;

    protected $table = 'people';

    protected $dates = [
        'birthDate'
    ];

    protected $fillable = [
        'firstName',
        'lastName',
        'salary',
        'login',
        'password',
        'birthDate',
        'super'
    ];

    protected $filterable = [
        'id',
        'firstName',
        'lastName',
        'login'
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * @inheritdoc
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function(Person $model)
        {
            $model->onSaving();
        });
    }

    /**
     * Prepare attributes before save
     */
    public function onSaving()
    {
        if (!$this->exists) {
            $this->token = str_random(60);
        }

        if ($this->isDirty('password')) {
            $this->password = Hash::make($this->password);
        }
    }

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

    /**
     * @inheritdoc
     */
    public function isSuperUser()
    {
        return $this->super;
    }

}
