<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\ApiBundle\Facade\TrashItemFacade;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;

/**
 * Class TrashItemTransformer
 */
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
        $facade->addLink('_self_restore',  $this->generateRoute(
            'open_orchestra_api_trashcan_restore',
            array('trashItemId' => $trashItem->getId())
        ));

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
