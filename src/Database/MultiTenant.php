<?php


namespace Soluto\MultiTenant\Database;

use RuntimeException;
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
                throw new RuntimeException("Current user must implement Tenant interface");
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
