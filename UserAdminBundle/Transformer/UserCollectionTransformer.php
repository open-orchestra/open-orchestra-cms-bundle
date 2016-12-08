<?php

namespace OpenOrchestra\UserAdminBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;

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
        $facade = $this->newFacade();

        foreach ($mixed as $user) {
            $facade->addUser($this->getTransformer('user')->transform($user));
        }

        if ($this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, UserInterface::ENTITY_TYPE)) {
            $facade->addLink('_self_add', $this->generateRoute('open_orchestra_user_admin_new'));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param null $source
     *
     * @return UserInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        $users = array();
        $usersFacade = $facade->getUsers();
        foreach ($usersFacade as $userFacade) {
            $user = $this->getTransformer('user')->reverseTransform($userFacade);
            if (null !== $user) {
                $users[] = $user;
            }
        }

        return $users;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user_collection';
    }
}
