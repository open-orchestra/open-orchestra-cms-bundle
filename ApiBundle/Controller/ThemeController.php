<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ModelInterface\Event\ThemeEvent;
use PHPOrchestra\ModelInterface\ThemeEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ThemeController
 *
 * @Config\Route("theme")
 */
class ThemeController extends Controller
{
    /**
     * @param int $themeId
     *
     * @Config\Route("/{themeId}", name="php_orchestra_api_theme_show")
     * @Config\Method({"GET"})
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($themeId)
    {
        $theme = $this->get('php_orchestra_model.repository.theme')->find($themeId);

        return $this->get('php_orchestra_api.transformer_manager')->get('theme')->transform($theme);
    }

    /**
     * @Config\Route("", name="php_orchestra_api_theme_list")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $themeCollection = $this->get('php_orchestra_model.repository.theme')->findAll();

        return $this->get('php_orchestra_api.transformer_manager')->get('theme_collection')->transform($themeCollection);
    }

    /**
     * @param int $themeId
     *
     * @Config\Route("/{themeId}/delete", name="php_orchestra_api_theme_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($themeId)
    {
        $theme = $this->get('php_orchestra_model.repository.theme')->find($themeId);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $this->dispatchEvent(ThemeEvents::THEME_DELETE, new ThemeEvent($theme));
        $dm->remove($theme);
        $dm->flush();

        return new Response('', 200);
    }
}
