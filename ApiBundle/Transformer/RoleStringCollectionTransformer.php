<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;

/**
 * Class RoleStringCollectionTransformer
 */
class RoleStringCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param array  $roleCollection
     * @param string $type
     *
     * @return FacadeInterface
     */
    public function transform($roleCollection, $type = null)
    {
        $facade = $this->newFacade();

        foreach ($roleCollection as $role => $translation) {
            $facade->addRole($this->getTransformer('role_string')->transform($role, $translation));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'role_string_collection';
    }
}
