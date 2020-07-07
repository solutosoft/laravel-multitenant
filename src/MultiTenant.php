<?php


namespace Solutosoft\MultiTenant;

use RuntimeException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

/**
 * @property boolean $disableTenantScope
 */
trait MultiTenant
{
    /**
     * @inheritdoc
     */
    public static function bootMultiTenant()
    {
        if (!property_exists(get_called_class(), 'disableTenantScope') || !static::$disableTenantScope) {
            static::addGlobalScope(new TenantScope());
        }

        static::creating(function(Model $model)
        {
            $model->applyTenant();
        });
    }

    /**
     * Sets tenant id column with current tenant
     *
     * @throws \Solutosoft\MultiTenant\TenantException
     */
    public function applyTenant()
    {
        $user = Auth::user();
        $tenantId = $this->getAttribute(Tenant::ATTRIBUTE_NAME);

        if (!$tenantId) {
            if ($user instanceof Tenant) {
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
