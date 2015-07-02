<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ApiBundle\Facade\RoleCollectionFacade;

/**
 * Class RoleCollectionTransformer
 */
class RoleCollectionTransformer extends AbstractTransformer
{
    /**
     * @param Collection $roleCollection
     *
     * @return FacadeInterface
     */
    public function transform($roleCollection)
    {
        $facade = new RoleCollectionFacade();

        foreach ($roleCollection as $role) {
            $facade->addRole($this->getTransformer('role')->transform($role));
        }

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_role_list',
            array()
        ));

        $facade->addLink('_self_add', $this->generateRoute(
            'open_orchestra_backoffice_role_new',
            array()
        ));

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
