<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\ApiBundle\Facade\TrashCanFacade;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Model\TrashCanInterface;

class TrashCanTransformer extends AbstractTransformer
{

    /**
     * @param mixed $trashcan
     *
     * @return TrashCanFacade
     * @throws TransformerParameterTypeException
     */
    public function transform($trashcan)
    {
        if (!$trashcan instanceof TrashCanInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = new TrashCanFacade();
        $facade->id = $trashcan->getId();
        $facade->deleteAt = $trashcan->getDeleteAt();
        $facade->name = $trashcan->getName();
        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'trashcan';
    }
}
