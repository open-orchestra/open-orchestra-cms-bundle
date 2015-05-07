<?php

namespace OpenOrchestra\ApiBundle\Context;

/**
 * Class GroupContext
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 *
 * This class is used to store the groups linked to a controller action through the whole request process
 */
class GroupContext
{
    const G_HIDE_ROLES = 'hide_roles';

    protected $groups = array();

    /**
     * @param string $group
     */
    public function addGroup($group)
    {
        if (!$this->hasGroup($group)) {
            $this->groups[] = $group;
        }
    }

    /**
     * @param array $groups
     */
    public function setGroups(array $groups = array())
    {
        $this->groups = $groups;
    }

    /**
     * @param string $group
     *
     * @return bool
     */
    public function hasGroup($group)
    {
        return in_array($group, array_values($this->groups));
    }
}
