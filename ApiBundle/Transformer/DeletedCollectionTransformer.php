<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ApiBundle\Facade\DeletedCollectionFacade;

/**
 * Class DeletedCollectionTransformer
 */
class DeletedCollectionTransformer extends AbstractTransformer
{
    /**
     * @param Collection $deletedCollection
     *
     * @return FacadeInterface
     */
    public function transform($deletedCollection)
    {
        $facade = new DeletedCollectionFacade();

        foreach ($deletedCollection as $deleted) {
            $deleted = $this->getTransformer('deleted')->transform($deleted);
            if (!is_null($deleted)) {
                $facade->addDeleted($deleted);
            }
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'deleted_collection';
    }
}
