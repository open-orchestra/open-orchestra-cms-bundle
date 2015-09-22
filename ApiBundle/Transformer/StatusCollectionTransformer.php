<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ApiBundle\Facade\StatusCollectionFacade;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;

/**
 * Class StatusCollectionTransformer
 */
class StatusCollectionTransformer extends AbstractTransformer
{
    /**
     * @param Collection               $statusCollection
     * @param StatusableInterface|null $document
     *
     * @return FacadeInterface|StatusCollectionFacade
     */
    public function transform($statusCollection, $document = null)
    {
        $facade = new StatusCollectionFacade();

        foreach ($statusCollection as $status) {
            $facade->addStatus($this->getTransformer('status')->transform($status, $document));
        }

        $facade->addLink('_self_add', $this->generateRoute(
            'open_orchestra_backoffice_status_new',
            array()
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'status_collection';
    }

}
