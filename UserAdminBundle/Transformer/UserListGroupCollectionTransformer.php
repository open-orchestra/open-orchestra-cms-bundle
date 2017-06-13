<?php

namespace OpenOrchestra\UserAdminBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\Backoffice\Model\GroupInterface;
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
            $facade->addUser($this->getTransformer('user_list_group')->cacheTransform($user));
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
