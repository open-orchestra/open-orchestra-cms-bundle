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
     * @param array|null $params
     *
     * @return FacadeInterface
     */
    public function transform($groupCollection, array $params = null)
    {
        $nbrGroupsUsers = is_array($params) && array_key_exists('nbrGroupsUsers') ? $params['nbrGroupsUsers'] : array();
        $facade = $this->newFacade();

        foreach ($groupCollection as $group) {
            $nbrGroupUsers = array_key_exists($group->getId(), $nbrGroupsUsers) ? $nbrGroupsUsers[$group->getId()] : 0;
            $facade->addGroup($this->getContext()->transform('group', $group, array('$nbrGroupUsers' => $nbrGroupUsers)));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param array|null      $params
     *
     * @return array
     */
    public function reverseTransform(FacadeInterface $facade, array $params = null)
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
