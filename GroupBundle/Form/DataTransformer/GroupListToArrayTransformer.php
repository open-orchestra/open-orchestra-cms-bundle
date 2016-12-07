<?php

namespace OpenOrchestra\GroupBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\GroupBundle\Repository\GroupRepository;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;

/**
 * Class GroupListToArrayTransformer
 */
class GroupListToArrayTransformer implements DataTransformerInterface
{
    protected $groupRepository;
    protected $availableSiteIds;

    /**
     * @param GroupRepository        $groupRepository
     * @param CurrentSiteIdInterface $contextManager
     */
    public function __construct(
        GroupRepository $groupRepository,
        CurrentSiteIdInterface $contextManager
    ) {
        $this->groupRepository = $groupRepository;
        $this->availableSiteIds = array();

        foreach ($contextManager->getAvailableSites() as $site) {
            $this->availableSiteIds[] = $site->getId();
        }
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

        if ($groups instanceof Collection) {
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
                    if (null !== $group && in_array($group->getSite()->getSiteId(), $this->availableSiteIds)) {
                        $value->add($group);
                    }
                }
            }
        }

        return $value;
    }
}
