<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class TranslationController
 *
 * @Config\Route("translation")
 *
 * @Api\Serialize()
 */
class TranslationController extends BaseController
{
    /**
     * @Config\Route("/{domain}", name="open_orchestra_api_translation")
     * @Config\Method({"GET"})
     *
     * @param string $domain
     * 
     * @return Response
     */
    public function getTranslationAction($domain)
    {
        $locale = $this->get('translator')->getLocale();
        $translations = $this->get('translator')->getCatalogue($locale)->all($domain);

        return $this->get('open_orchestra_api.transformer_manager')->get('translation')->transform($translations, $locale);
    }
}
