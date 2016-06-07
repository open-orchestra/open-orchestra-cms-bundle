<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class SiteTransformer
 */
class SiteTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $translationChoiceManager;

    /**
     * @param string                        $facadeClass
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TranslationChoiceManager      $translationChoiceManager
     */
    public function __construct(
        $facadeClass,
        AuthorizationCheckerInterface $authorizationChecker,
        TranslationChoiceManager $translationChoiceManager
    ){
        parent::__construct($facadeClass, $authorizationChecker);
        $this->translationChoiceManager = $translationChoiceManager;
    }

    /**
     * @param SiteInterface $site
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($site)
    {
        if (!$site instanceof SiteInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->id = $site->getId();
        $facade->siteId = $site->getSiteId();
        $facade->name = $site->getName();
        $facade->metaKeyword = $this->translationChoiceManager->choose($site->getMetaKeywords());
        $facade->metaDescription = $this->translationChoiceManager->choose($site->getMetaDescriptions());
        $facade->metaIndex = $site->getMetaIndex();
        $facade->metaFollow = $site->getMetaFollow();
        $facade->theme = $this->getTransformer('theme')->transform($site->getTheme());

        foreach ($site->getLanguages() as $language) {
            $facade->addLanguage($language);
        }

        foreach ($site->getBlocks() as $value) {
            $facade->addBlocks($value);
        }

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_site_show',
            array('siteId' => $site->getSiteId())
        ));

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_SITE)) {
            $facade->addLink('_self_delete', $this->generateRoute(
                'open_orchestra_api_site_delete',
                array('siteId' => $site->getSiteId())
            ));
        }

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_SITE)) {
            $facade->addLink('_self_form', $this->generateRoute(
                'open_orchestra_backoffice_site_form',
                array('siteId' => $site->getSiteId())
            ));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site';
    }
}
