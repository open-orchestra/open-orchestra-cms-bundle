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
     *
     * @return FacadeInterface
     */
    public function transform($trashItemCollection)
    {
        $facade = $this->newFacade();

        foreach ($trashItemCollection as $trashItem) {
            $facade->addElement($this->getTransformer('trash_item')->transform($trashItem));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param null            $source
     *
     * @return array
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        $trashItems = array();
        $trashItemsFacade = $facade->getTrashItems();
        foreach ($trashItemsFacade as $trashItemFacade) {
            $trashItem = $this->getTransformer('trash_item')->reverseTransform($trashItemFacade);
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
