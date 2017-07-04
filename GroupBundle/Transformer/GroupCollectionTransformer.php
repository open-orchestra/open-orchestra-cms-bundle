<?php

namespace OpenOrchestra\GroupBundle\Transformer;

use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class GroupCollectionTransformer
 */
class GroupCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param Collection $groupCollection
     * @param array      $params
     *
     * @return FacadeInterface
     */
    public function transform($groupCollection, array $params = array())
    {
        $nbrGroupsUsers = array_key_exists('nbrGroupsUsers', $params) ? $params['nbrGroupsUsers'] : array();
        $facade = $this->newFacade();

        foreach ($groupCollection as $group) {
            $facade->addGroup($this->getContext()->transform('group', $group, array('nbrGroupsUsers' => $nbrGroupsUsers)));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param array           $params
     *
     * @return array
     */
    public function reverseTransform(FacadeInterface $facade, array $params = array())
    {
        $groups = array();
        $groupsFacade = $facade->getGroups();
        foreach ($groupsFacade as $groupFacade) {
            $group = $this->getContext()->reverseTransform('group', $groupFacade);
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
