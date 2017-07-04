<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class TrashItemCollectionTransformer
 */
class TrashItemCollectionTransformer extends AbstractTransformer
{
    /**
     * @param Collection $trashItemCollection
     * @param array      $params
     *
     * @return FacadeInterface
     */
    public function transform($trashItemCollection, array $params = array())
    {
        $facade = $this->newFacade();

        foreach ($trashItemCollection as $trashItem) {
            $facade->addElement($this->getContext()->transform('trash_item', $trashItem));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param array           $params
     *
     * @return array
     */
    public function reverseTransform(FacadeInterface $facade, array $params = array())
    {
        $trashItems = array();
        $trashItemsFacade = $facade->getTrashItems();
        foreach ($trashItemsFacade as $trashItemFacade) {
            $trashItem = $this->getContext()->reverseTransform('trash_item', $trashItemFacade);
            if (null !== $trashItem) {
                $trashItems[] = $trashItem;
            }
        }

        return $trashItems;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'trash_item_collection';
    }
}
