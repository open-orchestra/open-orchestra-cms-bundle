<?php

namespace OpenOrchestra\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DashboardController
 *
 * @Config\Route("dashboard")
 *
 * @Api\Serialize()
 */
class DashboardController extends BaseController
{
    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_dashboard")
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     */
    public function listWidgetsAction(Request $request)
    {
        $widgetCollection = $this->container->getParameter('open_orchestra_backoffice.dashboard.widgets');

        return $this->get('open_orchestra_api.transformer_manager')->get('widget_collection')->transform($widgetCollection);
    }
}
