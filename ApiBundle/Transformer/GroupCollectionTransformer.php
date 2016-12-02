<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Model\GroupInterface;

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
        $facade = $this->newFacade();

        foreach ($groupCollection as $group) {
            $facade->addGroup($this->getTransformer('group')->transform($group));
        }

        if ($this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, GroupInterface::ENTITY_TYPE)) {
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
