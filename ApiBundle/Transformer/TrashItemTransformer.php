<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;
use OpenOrchestra\ModelInterface\Repository\TrashItemRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class TrashItemTransformer
 */
class TrashItemTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $trashItemRepository;

    /**
     * @param string                        $facadeClass
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TrashItemRepositoryInterface  $trashItemRepository
     */
    public function __construct(
        $facadeClass,
        AuthorizationCheckerInterface $authorizationChecker,
        TrashItemRepositoryInterface  $trashItemRepository
    ) {
        parent::__construct($facadeClass, $authorizationChecker);
        $this->trashItemRepository = $trashItemRepository;
    }

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
        $facade->addRight('can_delete', true);

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param null $source
     *
     * @return TrashItemInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
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
