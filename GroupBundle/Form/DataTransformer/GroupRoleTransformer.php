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
        $result = $this->groupRolesConfiguration;
        if (is_array($value)) {
            array_walk_recursive($result, function(&$item, $key) use($value) {
                $item = in_array($key, $value);
            });
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
