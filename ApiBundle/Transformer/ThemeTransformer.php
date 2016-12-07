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

        if ($this->hasGroup(CMSGroupContext::THEME_LINKS)) {

            if ($this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $theme)) {
                $facade->addLink('_self_delete', $this->generateRoute(
                    'open_orchestra_api_theme_delete',
                    array('themeId' => $theme->getId())
                ));
            }

            if ($this->authorizationChecker->isGranted(ContributionActionInterface::EDIT, $theme)) {
                $facade->addLink('_self_form', $this->generateRoute(
                    'open_orchestra_backoffice_theme_form',
                    array('themeId' => $theme->getId())
                ));
            }
        }

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
