<?php

namespace OpenOrchestra\UserAdminBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\UserBundle\Model\UserInterface;

/**
 * Class UserCollectionTransformer
 */
class UserCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param Collection $mixed
     * @param array      $params
     *
     * @return FacadeInterface
     */
    public function transform($mixed, array $params = array())
    {
        $facade = $this->newFacade();

        foreach ($mixed as $user) {
            $facade->addUser($this->getContext()->transform('user', $user));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param array           $params
     *
     * @return UserInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, array $params = array())
    {
        $users = array();
        $usersFacade = $facade->getUsers();
        foreach ($usersFacade as $userFacade) {
            $user = $this->getContext()->reverseTransform('user', $userFacade);
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
