<?php

namespace OpenOrchestra\UserAdminBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\UserAdminBundle\Facade\UserCollectionFacade;

/**
 * Class UserCollectionTransformer
 */
class UserCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param Collection $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new UserCollectionFacade();

        foreach ($mixed as $user) {
            $facade->addUser($this->getTransformer('user')->transform($user));
        }

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_USER)) {
            $facade->addLink('_self_add', $this->generateRoute('open_orchestra_user_admin_new'));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user_collection';
    }
}
