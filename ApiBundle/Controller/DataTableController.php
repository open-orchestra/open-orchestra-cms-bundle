<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class DataTableController
 *
 * @Config\Route("datatable")
 *
 * @Api\Serialize()
 */
class DataTableController extends BaseController
{
    /**
     * @Config\Route("/translation", name="open_orchestra_api_datatable_translation")
     * @Config\Method({"GET"})
     *
     * @return Response
     */
    public function getTranslationAction()
    {
        return $this->get('open_orchestra_api.transformer_manager')->get('datatable_translation')->transform("datatable");
    }
}
