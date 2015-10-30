<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ApiBundle\Facade\RoleCollectionFacade;

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
        $facade = new RoleCollectionFacade();

        foreach ($roleCollection as $role) {
            $facade->addRole($this->getTransformer('role')->transform($role));
        }

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_role_list',
            array()
        ));

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_ROLE)) {
            $facade->addLink('_self_add', $this->generateRoute(
                'open_orchestra_backoffice_role_new',
                array()
            ));
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
