<?php

namespace OpenOrchestra\GroupBundle\BusinessRules\Strategies;

use OpenOrchestra\Backoffice\BusinessRules\Strategies\AbstractBusinessRulesStrategy;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\UserBundle\Repository\UserRepositoryInterface;

/**
 * class GroupMemberStrategy
 */
class GroupMemberStrategy extends AbstractBusinessRulesStrategy
{

    /**
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param mixed $entity
     *
     * @return boolean
     */
    public function supportEntity($entity){
        return $entity instanceof GroupInterface;
    }

    /**
     * @return array
     */
    public function getActions(){
        return array(
            ContributionActionInterface::DELETE => 'canDelete',
        );
    }

    /**
     * @param GroupInterface $group
     * @param array          $parameters
     *
     * @return boolean
     */
    public function canDelete(GroupInterface $group, array $parameters){
        $nbrGroupsUsers = $parameters;
        if (!array_key_exists($group->getId(), $nbrGroupsUsers)) {
            $nbrGroupsUsers = $this->userRepository->getCountsUsersByGroups(array($group->getId()));
        }

        return 0 === $nbrGroupsUsers[$group->getId()];
    }
}
