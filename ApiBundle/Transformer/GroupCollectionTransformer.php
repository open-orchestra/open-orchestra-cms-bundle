<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ApiBundle\Facade\GroupCollectionFacade;

/**
 * Class GroupCollectionTransformer
 */
class GroupCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $groupCollection
     *
     * @return FacadeInterface
     */
    public function transform($groupCollection)
    {
        $facade = new GroupCollectionFacade();

        foreach ($groupCollection as $group) {
            $facade->addGroup($this->getTransformer('group')->transform($group));
        }

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_group_list',
            array()
        ));

        $facade->addLink('_self_add', $this->generateRoute(
            'open_orchestra_backoffice_group_new',
            array()
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'group_collection';
    }
}
