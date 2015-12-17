<?php

namespace OpenOrchestra\Backoffice\Collector;

/**
 * Class RoleCollector
 */
interface RoleCollectorInterface
{
    /**
     * @return array
     */
    public function getRoles();

    /**
     * @param string $role
     */
    public function addRole($role);

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role);
}