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
     * @param array      $nbrGroupsUsers
     *
     * @return FacadeInterface
     */
    public function transform($groupCollection, array $nbrGroupsUsers = array())
    {
        $facade = $this->newFacade();

        foreach ($groupCollection as $group) {
            $facade->addGroup($this->getTransformer('group')->transform($group, (array_key_exists($group->getId(), $nbrGroupsUsers)) ? $nbrGroupsUsers[$group->getId()] : 0));
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
     * @param FacadeInterface $facade
     * @param null            $source
     *
     * @return array
     */
    public function reverseTransform(FacadeInterface $facade, $source = NULL)
    {
        $groups = array();
        $groupsFacade = $facade->getGroups();
        foreach ($groupsFacade as $groupFacade) {
            $group = $this->getTransformer('group')->reverseTransform($groupFacade);
            if (null !== $group) {
                $groups[] = $group;
            }
        }

        return $groups;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'group_collection';
    }
}
