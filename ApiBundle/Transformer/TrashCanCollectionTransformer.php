<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\ApiBundle\Facade\TrashCanCollectionFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

class TrashCanCollectionTransformer extends AbstractTransformer
{
    /**
     * @param Collection $trashCanCollection
     *
     * @return FacadeInterface
     */
    public function transform($trashCanCollection)
    {
        $facade = new TrashCanCollectionFacade();

        foreach ($trashCanCollection as $trashCan) {
            $facade->addElement($this->getTransformer('trashcan')->transform($trashCan));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'trashcan_collection';
    }
}
