<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\HandleRequestDataTable;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\ThemeEvent;
use OpenOrchestra\ModelInterface\ThemeEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class ThemeController
 *
 * @Config\Route("theme")
 *
 * @Api\Serialize()
 */
class ThemeController extends BaseController
{
    use HandleRequestDataTable;

    /**
     * @param int $themeId
     *
     * @Config\Route("/{themeId}", name="open_orchestra_api_theme_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_THEME')")
     *
     * @return FacadeInterface
     */
    public function showAction($themeId)
    {
        $theme = $this->get('open_orchestra_model.repository.theme')->find($themeId);

        return $this->get('open_orchestra_api.transformer_manager')->get('theme')->transform($theme);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_theme_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_THEME')")
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $mapping = $this
            ->get('open_orchestra.annotation_search_reader')
            ->extractMapping($this->container->getParameter('open_orchestra_model.document.theme.class'));
        $repository = $this->get('open_orchestra_model.repository.theme');
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('theme_collection');

        return $this->handleRequestDataTable($request, $repository, $mapping, $collectionTransformer);
    }

    /**
     * @param int $themeId
     *
     * @Config\Route("/{themeId}/delete", name="open_orchestra_api_theme_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_DELETE_THEME')")
     *
     * @return Response
     */
    public function deleteAction($themeId)
    {
        $theme = $this->get('open_orchestra_model.repository.theme')->find($themeId);
        $dm = $this->get('object_manager');
        $this->dispatchEvent(ThemeEvents::THEME_DELETE, new ThemeEvent($theme));
        $dm->remove($theme);
        $dm->flush();

        return array();
    }
}
