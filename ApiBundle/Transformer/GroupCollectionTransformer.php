<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Facade\GroupCollectionFacade;

/**
 * Class GroupCollectionTransformer
 */
class GroupCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param Collection $groupCollection
     *
     * @return FacadeInterface
     */
    public function transform($groupCollection)
    {
        $facade = new GroupCollectionFacade();

        foreach ($groupCollection as $group) {
            $facade->addGroup($this->getTransformer('group')->transform($group));
        }

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_GROUP)) {
            $facade->addLink('_self', $this->generateRoute(
                'open_orchestra_api_group_list',
                array()
            ));
        }

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_GROUP)) {
            $facade->addLink('_self_add', $this->generateRoute(
                'open_orchestra_backoffice_group_new',
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
        return 'group_collection';
    }
}
