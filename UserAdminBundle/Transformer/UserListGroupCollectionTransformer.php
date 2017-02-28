<?php

namespace OpenOrchestra\UserAdminBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;

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
