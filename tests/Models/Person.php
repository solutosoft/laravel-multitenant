<?php

namespace Solutosoft\MultiTenant\Tests\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Solutosoft\MultiTenant\MultiTenant;
use Solutosoft\MultiTenant\Tenant;

class Person extends Model implements Tenant
{
    use MultiTenant, Authenticatable;

    protected $table = 'people';

    protected $fillable = [
        'firstName',
        'lastName',
        'login',
        'password',
        'active'
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
    public function getTenantColumn()
    {
        return 'organization_id';
    }

    /**
     * @inheritdoc
     */
    public function isRoot()
    {
        return $this->id === 1;
    }

    /**
     * Scope a query to only include active users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

}
