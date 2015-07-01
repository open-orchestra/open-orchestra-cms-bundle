<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ModelInterface\Event\ThemeEvent;
use OpenOrchestra\ModelInterface\ThemeEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

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
     * @Config\Security("has_role('ROLE_ACCESS_THEME')")
     *
     * @Api\Serialize()
     *
     * @return \OpenOrchestra\BaseApi\Facade\FacadeInterface
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
     * @Config\Security("has_role('ROLE_ACCESS_THEME')")
     *
     * @Api\Serialize()
     *
     * @return \OpenOrchestra\BaseApi\Facade\FacadeInterface
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
     * @Config\Security("has_role('ROLE_ACCESS_THEME')")
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
