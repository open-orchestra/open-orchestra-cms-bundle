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
     * @return string
     */
    public function getName()
    {
        return 'trash_item_collection';
    }
}
