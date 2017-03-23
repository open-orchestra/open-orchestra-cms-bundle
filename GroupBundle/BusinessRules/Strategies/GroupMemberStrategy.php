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
     * @param UserRepositoryInterface $repositoryUser
     */
    public function __construct(UserRepositoryInterface $repositoryUser)
    {
        $this->repositoryUser = $repositoryUser;
    }

    /**
     * @param mixed $object
     *
     * @return boolean
     */
    public function supportObject($object){
        return $object instanceof GroupInterface;
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
     * @param array          $optionalParameters
     *
     * @return boolean
     */
    protected function canDelete(GroupInterface $group, array $optionalParameters){
        $nbrGroupsUsers = $optionalParameters;
        if (!array_key_exists($group->getId(), $nbrGroupsUsers)) {
            $nbrGroupsUsers = $this->repositoryUser->getCountsUsersByGroups(array($group->getId()));
        }

        return 0 === $nbrGroupsUsers[$group->getId()];
    }
}
