<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
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

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_ROLE)) {
            $facade->addLink('_self', $this->generateRoute(
                'open_orchestra_api_role_list_by_type',
                array('type' => $type)
            ));
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
