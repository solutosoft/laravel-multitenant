<?php

namespace Soluto\MultiTenant\Database;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    /**
     * @inheritdoc
     */
    public function apply(Builder $builder, Model $model)
    {
        $user = Auth::user();

        if ($user instanceof Tenant) {
            $builder->where($model->getQualifiedTenantName(), $user->getTenantId());
        } else {
            throw new TenantException("Current user must implement Tenant interface");
        }
    }
}
