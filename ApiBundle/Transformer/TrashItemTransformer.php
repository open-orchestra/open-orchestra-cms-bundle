<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\ApiBundle\Facade\TrashItemFacade;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;

class TrashItemTransformer extends AbstractTransformer
{
    /**
     * @param mixed $trashItem
     *
     * @return TrashItemFacade
     * @throws TransformerParameterTypeException
     */
    public function transform($trashItem)
    {
        if (!$trashItem instanceof TrashItemInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = new TrashItemFacade();
        $facade->id = $trashItem->getId();
        $facade->deletedAt = $trashItem->getDeletedAt();
        $facade->name = $trashItem->getName();
        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'trash_item';
    }
}
