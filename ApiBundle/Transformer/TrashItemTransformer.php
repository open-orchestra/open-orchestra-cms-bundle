<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;
use OpenOrchestra\ModelInterface\Repository\TrashItemRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class TrashItemTransformer
 */
class TrashItemTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $trashItemRepository;
    protected $validator;

    /**
     * @param string                        $facadeClass
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TrashItemRepositoryInterface  $trashItemRepository
     * @param ValidatorInterface            $validator
     */
    public function __construct(
        $facadeClass,
        AuthorizationCheckerInterface $authorizationChecker,
        TrashItemRepositoryInterface  $trashItemRepository,
        ValidatorInterface  $validator
    ) {
        parent::__construct($facadeClass, $authorizationChecker);
        $this->trashItemRepository = $trashItemRepository;
        $this->validator = $validator;
    }

    /**
     * @param mixed $trashItem
     * @param array $params
     *
     * @return FacadeInterface
     * @throws TransformerParameterTypeException
     */
    public function transform($trashItem, array $params = array())
    {
        if (!$trashItem instanceof TrashItemInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();
        $facade->id = $trashItem->getId();
        $facade->deletedAt = $trashItem->getDeletedAt();
        $facade->name = $trashItem->getName();
        $facade->type = $trashItem->getType();
        $facade->entityId = $trashItem->getEntityId();
        $facade->siteId = $trashItem->getSiteId();
        $facade->addRight('can_delete', (
            $this->authorizationChecker->isGranted(ContributionActionInterface::TRASH_PURGE, $trashItem) &&
            0 === count($this->validator->validate($trashItem, null, array('remove')))
        ));

        $facade->addRight('can_restore', $this->authorizationChecker->isGranted(ContributionActionInterface::TRASH_RESTORE, $trashItem));

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param array           $params
     *
     * @return TrashItemInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, array $params = array())
    {
        if (null !== $facade->id) {
            return $this->trashItemRepository->find($facade->id);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'trash_item';
    }
}
