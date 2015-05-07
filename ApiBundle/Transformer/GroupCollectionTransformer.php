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
     * @param ArrayCollection $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new GroupCollectionFacade();

        foreach ($mixed as $group) {
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

        $facade->addLink('_translate', $this->generateRoute('open_orchestra_api_translate'));

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
