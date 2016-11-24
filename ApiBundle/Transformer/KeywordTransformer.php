<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class KeywordTransformer
 */
class KeywordTransformer extends AbstractSecurityCheckerAwareTransformer
{
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

        $facade->addRight('can_edit', $this->authorizationChecker->isGranted(ContributionActionInterface::EDIT, $keyword));
        $can_delete =
            $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $keyword) && !$keyword->isUsed();
        $facade->addRight('can_delete', $can_delete);

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'keyword';
    }
}
