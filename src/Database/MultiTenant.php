<?php


namespace Soluto\MultiTenant\Database;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

trait MultiTenant
{

    /**
     * @inheritdoc
     */
    public static function bootMultiTenant()
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function(Model $model)
        {
            $model->applyTenant();
        });
    }

    /**
     * Get the table qualified tenant column name.
     *
     * @return string
     */
    public function getQualifiedTenantName()
    {
        return $this->getTable().'.'.Tenant::ATTRIBUTE_NAME;
    }

    /**
     * Sets tenant id column with current tenant
     *
     * @throws \Soluto\MultiTenant\Database\TenantException
     */
    public function applyTenant()
    {
        $user = Auth::user();
        $valid =  (!Auth::guest() && $user instanceof Tenant);
        $tenantId = $this->getAttribute(Tenant::ATTRIBUTE_NAME);

        if (!$tenantId) {
            if ($valid) {
                $this->setAttribute(Tenant::ATTRIBUTE_NAME, $user->getTenantId());
            } else {
                throw new TenantException("Current user must implement Tenant interface");
            }
        }
    }

    /**
     * Remove a registered Tenant global scope.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function withoutTenant()
    {
       return static::withoutGlobalScope(TenantScope::class);
    }

}
