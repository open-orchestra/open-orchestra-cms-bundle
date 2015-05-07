<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ModelInterface\Model\SiteInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
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
     * @Config\Route("/language/{language}", name="open_orchestra_api_context_language")
     * @Api\Serialize()
     *
     * @return array
     */
    public function languageAction($language)
    {
        $contextManager = $this->get('open_orchestra_backoffice.context_manager');

        $contextManager->setCurrentLocale($language);

        return array('success' => true);
    }

    /**
     * Switch context current site
     *
     * @param string $siteId
     * @param string $siteName
     *
     * @Config\Route("/site/{siteId}/{siteName}", name="open_orchestra_api_context_site")
     * @Api\Serialize()
     *
     * @return array
     */
    public function siteAction($siteId, $siteName)
    {
        $contextManager = $this->get('open_orchestra_backoffice.context_manager');

        /** @var SiteInterface $site */
        $site = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($siteId);
        $contextManager->setCurrentsite($site->getSiteId(), $site->getName(), $site->getDefaultLanguage());

        return array('success' => true);
    }
}
