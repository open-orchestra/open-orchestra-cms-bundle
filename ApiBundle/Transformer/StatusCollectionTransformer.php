<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;

/**
 * Class StatusCollectionTransformer
 */
class StatusCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param Collection               $statusCollection
     * @param StatusableInterface|null $document
     *
     * @return FacadeInterface
     */
    public function transform($statusCollection, $document = null)
    {
        $facade = $this->newFacade();

        foreach ($statusCollection as $status) {
            $facade->addStatus($this->getTransformer('status')->transform($status, $document));
        }

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_STATUS)) {
            $facade->addLink('_self_add', $this->generateRoute(
                'open_orchestra_backoffice_status_new',
                array()
            ));
        }

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
