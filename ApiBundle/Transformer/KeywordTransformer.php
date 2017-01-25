<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class KeywordTransformer
 */
class KeywordTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $keywordRepository;

    /**
     * @param string                        $facadeClass
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param KeywordRepositoryInterface    $keywordRepository
     */
    public function __construct(
        $facadeClass,
        AuthorizationCheckerInterface $authorizationChecker,
        KeywordRepositoryInterface    $keywordRepository
    ) {
        parent::__construct($facadeClass, $authorizationChecker);
        $this->keywordRepository = $keywordRepository;
    }

    /**
     * @param KeywordInterface $keyword
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($keyword)
    {
        if (!$keyword instanceof KeywordInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->id = $keyword->getId();
        $facade->label = $keyword->getLabel();
        $facade->label = $keyword->getLabel();
        $facade->numberUse = $keyword->countUse();

        $facade->addRight('can_edit', $this->authorizationChecker->isGranted(ContributionActionInterface::EDIT, $keyword));
        $can_delete =
            $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $keyword) && !$keyword->isUsed();
        $facade->addRight('can_delete', $can_delete);

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param null $source
     *
     * @return KeywordInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        if (null !== $facade->id) {
            return $this->keywordRepository->find($facade->id);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'keyword';
    }
}
