<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Model\ThemeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ApiBundle\Context\CMSGroupContext;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class ThemeTransformer
 */
class ThemeTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param ThemeInterface $theme
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($theme)
    {
        if (!$theme instanceof ThemeInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->id = $theme->getId();
        $facade->name = $theme->getName();

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'theme';
    }
}
