<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class DeletedController
 *
 * @Config\Route("deleted")
 */
class DeletedController extends BaseController
{
    /**
     * @Config\Route("/list", name="open_orchestra_api_deleted_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_DELETED')")
     *
     * @Api\Serialize()
     *
     * @return Response
     */
    public function listAction()
    {
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        /** @var array $nodes */
        $nodes = $this->get('open_orchestra_model.repository.node')->findDeletedInLastVersionBySiteId($siteId);
        $contents = $this->get('open_orchestra_model.repository.content')->findAllDeleted();

        $deleted = array_merge($nodes, $contents);

        return $this->get('open_orchestra_api.transformer_manager')->get('deleted_collection')->transform($deleted);
    }
}
