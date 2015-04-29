<?php

namespace OpenOrchestra\ApiBundle\Context;

/**
 * Class GroupContext
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
