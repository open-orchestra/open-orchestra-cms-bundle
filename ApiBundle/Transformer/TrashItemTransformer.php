<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;

/**
 * Class TrashItemTransformer
 */
class TrashItemTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param mixed $trashItem
     *
     * @return FacadeInterface
     * @throws TransformerParameterTypeException
     */
    public function transform($trashItem)
    {
        if (!$trashItem instanceof TrashItemInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();
        $facade->id = $trashItem->getId();
        $facade->deletedAt = $trashItem->getDeletedAt();
        $facade->name = $trashItem->getName();

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_RESTORE)) {
            $facade->addLink('_self_restore',  $this->generateRoute(
                'open_orchestra_api_trashcan_restore',
                array('trashItemId' => $trashItem->getId())
            ));
        }

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
