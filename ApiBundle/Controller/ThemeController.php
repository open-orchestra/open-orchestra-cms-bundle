<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\ThemeEvent;
use OpenOrchestra\ModelInterface\ThemeEvents;
use OpenOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ThemeController
 *
 * @Config\Route("theme")
 */
class ThemeController extends BaseController
{
    /**
     * @param int $themeId
     *
     * @Config\Route("/{themeId}", name="open_orchestra_api_theme_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_PANEL_THEME')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($themeId)
    {
        $theme = $this->get('open_orchestra_model.repository.theme')->find($themeId);

        return $this->get('open_orchestra_api.transformer_manager')->get('theme')->transform($theme);
    }

    /**
     * @Config\Route("", name="open_orchestra_api_theme_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_PANEL_THEME')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $themeCollection = $this->get('open_orchestra_model.repository.theme')->findAll();

        return $this->get('open_orchestra_api.transformer_manager')->get('theme_collection')->transform($themeCollection);
    }

    /**
     * @param int $themeId
     *
     * @Config\Route("/{themeId}/delete", name="open_orchestra_api_theme_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_PANEL_THEME')")
     *
     * @return Response
     */
    public function deleteAction($themeId)
    {
        $theme = $this->get('open_orchestra_model.repository.theme')->find($themeId);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $this->dispatchEvent(ThemeEvents::THEME_DELETE, new ThemeEvent($theme));
        $dm->remove($theme);
        $dm->flush();

        return new Response('', 200);
    }
}
