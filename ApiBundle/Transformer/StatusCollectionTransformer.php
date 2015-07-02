<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ApiBundle\Facade\StatusCollectionFacade;
use OpenOrchestra\ModelInterface\Model\StatusInterface;

/**
 * Class StatusCollectionTransformer
 */
class StatusCollectionTransformer extends AbstractTransformer
{
    /**
     * @param Collection           $statusCollection
     * @param StatusInterface|null $currentStatus
     *
     * @return FacadeInterface|StatusCollectionFacade
     */
    public function transform($statusCollection, $currentStatus = null)
    {
        $facade = new StatusCollectionFacade();

        foreach ($statusCollection as $status) {
            $facade->addStatus($this->getTransformer('status')->transform($status, $currentStatus));
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
