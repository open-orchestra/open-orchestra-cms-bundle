<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\BusinessRules\BusinessRulesManager;
use OpenOrchestra\Backoffice\BusinessRules\Strategies\BusinessActionInterface;
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
    protected $businessRulesManager;

    /**
     * @param string                        $facadeClass
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param KeywordRepositoryInterface    $keywordRepository
     * @param BusinessRulesManager          $businessRulesManager
     */
    public function __construct(
        $facadeClass,
        AuthorizationCheckerInterface $authorizationChecker,
        KeywordRepositoryInterface $keywordRepository,
        BusinessRulesManager $businessRulesManager
    ) {
        $this->keywordRepository = $keywordRepository;
        $this->businessRulesManager = $businessRulesManager;
        parent::__construct($facadeClass, $authorizationChecker);
    }

    /**
     * @param KeywordInterface $keyword
     * @param array            $params
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($keyword, array $params = array())
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
        $facade->addRight('can_delete', $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $keyword) &&
            $this->businessRulesManager->isGranted(BusinessActionInterface::DELETE, $keyword));

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param array           $params
     *
     * @return KeywordInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, array $params = array())
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
