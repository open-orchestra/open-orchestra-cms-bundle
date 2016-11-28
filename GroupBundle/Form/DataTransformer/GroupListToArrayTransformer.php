<?php

namespace OpenOrchestra\GroupBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\UserBundle\Repository\GroupRepository;

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

        foreach($groups as $group) {
            $value[$group->getId()] = true;
        }

        return $value;
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

        foreach($groups as $id => $selected) {
            $value->add($this->groupRepository->find($id));
        }

        return $value;
    }
}
