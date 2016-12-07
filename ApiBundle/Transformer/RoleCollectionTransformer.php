<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;

/**
 * Class RoleCollectionTransformer
 */
class RoleCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param Collection $roleCollection
     *
     * @return FacadeInterface
     */
    public function transform($roleCollection)
    {
        $facade = $this->newFacade();

        foreach ($roleCollection as $role) {
            $facade->addRole($this->getTransformer('role')->transform($role));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'role_collection';
    }
}
