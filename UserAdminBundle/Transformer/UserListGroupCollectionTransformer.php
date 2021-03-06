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
     * @param Collection $mixed
     * @param array      $params
     *
     * @return FacadeInterface
     */
    public function transform($mixed, array $params = array())
    {
        $facade = $this->newFacade();

        foreach ($mixed as $user) {
            $facade->addUser($this->getContext()->transform('user_list_group', $user));
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
