<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ApiBundle\Facade\StatusCollectionFacade;

/**
 * Class StatusCollectionTransformer
 */
class StatusCollectionTransformer extends AbstractTransformer
{
    /**
     * @param \Doctrine\Common\Collections\Collection                  $statusCollection
     * @param \OpenOrchestra\ModelInterface\Model\StatusInterface|null $currentStatus
     *
     * @return \OpenOrchestra\BaseApi\Facade\FacadeInterface|StatusCollectionFacade
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
