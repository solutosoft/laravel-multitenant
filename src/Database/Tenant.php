<?php

namespace Soluto\MultiTenant\Database;

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
    const ATTRIBUTE_NAME = 'tenant_id';

    /**
     * Get then tenant id
     *
     * @return integer
     */
    public function getTenantId();

    /**
     * Whether the is root tenant.
     *
     * @return boolean whether tenant is master
     */
    public function isRoot();
}
