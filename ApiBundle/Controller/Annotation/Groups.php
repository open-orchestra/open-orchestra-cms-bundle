<?php

namespace OpenOrchestra\ApiBundle\Controller\Annotation;

/**
 * Class Groups
 *
 * @Annotation
 * @Target({"METHOD"})
 */
class Groups
{
    public $groups = array();

    /**
     * @param array $data
     */
    public function __construct($data)
    {
        if (isset($data['value'])) {
            $this->groups = is_array($data['value']) ? $data['value'] : array_map('trim', explode(',', $data['value']));
        }
    }
}
