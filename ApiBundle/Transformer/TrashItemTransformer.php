<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

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
        $facade->type = $trashItem->getType();

        if ($this->authorizationChecker->isGranted(ContributionActionInterface::TRASH_RESTORE, $trashItem)) {
            $facade->addLink('_self_restore',  $this->generateRoute(
                'open_orchestra_api_trashcan_restore',
                array('trashItemId' => $trashItem->getId())
            ));
        }

        if ($this->authorizationChecker->isGranted(ContributionActionInterface::TRASH_PURGE, $trashItem)) {
            $facade->addLink('_self_remove',  $this->generateRoute(
                'open_orchestra_api_trashcan_remove',
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
