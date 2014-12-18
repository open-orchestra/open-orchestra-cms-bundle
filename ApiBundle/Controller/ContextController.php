<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ModelInterface\Model\SiteInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class ContextController
 *
 * @Config\Route("context")
 */
class ContextController extends Controller
{
    /**
     * Switch context language
     *
     * @param string $language
     *
     * @Config\Route("/language/{language}", name="php_orchestra_api_context_language")
     * @Api\Serialize()
     *
     * @return array
     */
    public function languageAction($language)
    {
        $contextManager = $this->get('php_orchestra_backoffice.context_manager');

        $contextManager->setCurrentLocale($language);

        return array('success' => true);
    }

    /**
     * Switch context current site
     *
     * @param string $siteId
     * @param string $siteDomain
     *
     * @Config\Route("/site/{siteId}/{siteDomain}", name="php_orchestra_api_context_site")
     * @Api\Serialize()
     *
     * @return array
     */
    public function siteAction($siteId, $siteDomain)
    {
        $contextManager = $this->get('php_orchestra_backoffice.context_manager');

        /** @var SiteInterface $site */
        $site = $this->get('php_orchestra_model.repository.site')->findOneBySiteId($siteId);
        $contextManager->setCurrentsite($site->getSiteId(), $site->getDomain(), $site->getDefaultLanguage());

        return array('success' => true);
    }
}
