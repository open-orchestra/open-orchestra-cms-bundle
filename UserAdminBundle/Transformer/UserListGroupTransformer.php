<?php

namespace OpenOrchestra\UserAdminBundle\Transformer;

use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\UserBundle\Document\User;

/**
 * Class UserListGroupTransformer
 */
class UserListGroupTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param User $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = $this->newFacade();

        $facade->username = $mixed->getUsername();
        $facade->firstName = $mixed->getFirstName();
        $facade->lastName = $mixed->getLastName();
        $facade->email = $mixed->getEmail();

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
