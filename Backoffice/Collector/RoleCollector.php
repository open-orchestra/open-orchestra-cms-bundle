<?php

namespace OpenOrchestra\Backoffice\Collector;

/**
 * Class RoleCollector
 */
class RoleCollector
{
    protected $roles = array();

    /**
     * @return array
     */
    public function getRoles()
    {
        return array_unique($this->roles);
    }

    /**
     * @param string $role
     */
    public function addRole($role)
    {
        $this->roles[] = $role;
    }
}
