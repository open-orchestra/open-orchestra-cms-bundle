<?php

namespace OpenOrchestra\UserAdminBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;

/**
 * Class UserListGroupCollectionTransformer
 */
class UserListGroupCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param Collection          $mixed
     * @param GroupInterface|null $group
     *
     * @return FacadeInterface
     */
    public function transform($mixed, GroupInterface $group = null)
    {
        $facade = $this->newFacade();

        foreach ($mixed as $user) {
            $facade->addUser($this->getTransformer('user_list_group')->transform($user, $group));
        }

        if (null !== $group && $this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_USER)) {
            $facade->addLink('_list_without_group', $this->generateRoute(
                'open_orchestra_api_user_list_by_username_without_group',
                array(
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
        return 'user_list_group_collection';
    }
}
