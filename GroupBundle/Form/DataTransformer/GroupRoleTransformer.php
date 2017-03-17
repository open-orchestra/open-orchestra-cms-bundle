<?php

namespace OpenOrchestra\GroupBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class GroupRoleTransformer
 */
class GroupRoleTransformer implements DataTransformerInterface
{
    protected $groupRolesConfiguration;

    /**
     * @param array $groupRolesConfiguration
     */
    public function __construct(
        array $groupRolesConfiguration
    ) {
        $this->groupRolesConfiguration = $groupRolesConfiguration;
    }

    /**
     * Transform an array of roles to choices
     *
     * @param array $value
     *
     * @return array
     */
    public function transform($value)
    {
        foreach ($this->groupRolesConfiguration as $tables) {
            foreach ($tables as $package => $rows) {
                foreach ($rows as $row => $roles) {
                    foreach ($roles as $role => $configuration) {
                        $result[$package][$row][$role] = is_array($value) ? in_array($role, $value) : false;
                    }
                }
            }
        }

        return array('roles_collections' => $result);
    }

    /**
     * Transform an array choices to array of roles
     *
     * @param array $value
     *
     * @return array
     */
    public function reverseTransform($value)
    {
        $result = array();
        if (is_array($value)) {
            array_walk_recursive($value, function($item, $key) use (&$result) {
                if ($item) {
                    $result[] = $key;
                }
            });
        }

        return $result;
    }
}
