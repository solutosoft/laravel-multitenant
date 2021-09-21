<?php

namespace Solutosoft\MultiTenant;

/**
 * Defines set of methods to be implemented by a tenant.
 *
 * @author Leandro Guindani Gehlen <leandrogehlen@gmail.com>
 */
interface Tenant
{
    /**
     * The name of the "tenant" column.
     *
     * @var string
     */
    const TENANT_ID = 'tenant_id';

    /**
     * Get then tenant id
     *
     * @return integer
     */
    public function getTenantId();


    /**
     * Get the name of the "tenant" column.
     *
     * @return string|null
     */
    public function getTenantColumn();
}
