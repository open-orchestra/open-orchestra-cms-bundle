<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ApiBundle\Facade\DeletedFacade;
use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Facade\DeletedCollectionFacade;

/**
 * Class DeletedCollectionTransformer
 */
class DeletedCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new DeletedCollectionFacade();

        foreach ($mixed as $deleted) {
            $deleted = $this->getTransformer('deleted')->transform($deleted);
            if (!is_null($deleted)) {
                $facade->addDeleted($deleted);
            }
        }

        $deletedFacade = $facade->getDeleteds();

        usort($deletedFacade, function(DeletedFacade $a,DeletedFacade $b) {
            return ($b->updatedAt < $a->updatedAt)? -1 : 1;
        });

        $facade->setDeleteds($deletedFacade);

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
