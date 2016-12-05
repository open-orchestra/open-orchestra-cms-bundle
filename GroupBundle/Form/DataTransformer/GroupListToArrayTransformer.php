<?php

namespace OpenOrchestra\GroupBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\GroupBundle\Repository\GroupRepository;

/**
 * Class GroupListToArrayTransformer
 */
class GroupListToArrayTransformer implements DataTransformerInterface
{
    /**
     * @param GroupRepository $groupRepository
     */
    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * Transform an ArrayCollection of groups to array of group id
     *
     * @param ArrayCollection $groups
     *
     * @return array
     */
    public function transform($groups)
    {
        $value = array();
        if (is_array($groups)) {
            foreach($groups as $group) {
                $value[$group->getId()] = array('group' => true);
            }
        }
        return array('groups_collection' => $value);
    }

    /**
     * Transform an array of group id to ArrayCollection of groups
     *
     * @param array $groups
     *
     * @return ArrayCollection
     */
    public function reverseTransform($groups)
    {
        $value = new ArrayCollection();

        if (is_array($groups) && array_key_exists('groups_collection', $groups)) {
            $groups = $groups['groups_collection'];
            foreach ($groups as $groupId => $group) {
                if (array_key_exists('group', $group) && $group['group']) {
                    $group = $this->groupRepository->find($groupId);
                    if (null !== $group) {
                        $value->add($group);
                    }
                }
            }
        }

        return $value;
    }
}
