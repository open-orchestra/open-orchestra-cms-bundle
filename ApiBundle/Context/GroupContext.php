<?php

namespace OpenOrchestra\ApiBundle\Context;


/**
 * Class GroupContext
 */
class GroupContext
{
    protected $groups = array();

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
