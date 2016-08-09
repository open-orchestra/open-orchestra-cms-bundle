<?php

namespace OpenOrchestra\UserAdminBundle\Transformer;

use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\UserBundle\Document\User;

/**
 * Class UserListGroupTransformer
 */
class UserListGroupTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param User                $mixed
     * @param GroupInterface|null $group
     *
     * @return FacadeInterface
     */
    public function transform($mixed, GroupInterface $group = null)
    {
        $facade = $this->newFacade();

        $facade->username = $mixed->getUsername();
        $facade->firstName = $mixed->getFirstName();
        $facade->lastName = $mixed->getLastName();
        $facade->email = $mixed->getEmail();

        if (null !== $group && $this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_USER)) {
            $facade->addLink('_self_delete', $this->generateRoute(
                'open_orchestra_api_user_remove_group',
                array(
                    'userId' => $mixed->getId(),
                    'groupId' => $group->getId()
                )
            ));
            $facade->addLink('_self_add', $this->generateRoute(
                'open_orchestra_api_user_add_group',
                array(
                    'userId' => $mixed->getId(),
                    'groupId' => $group->getId()
                )
            ));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user_list_group';
    }

}
