<?php

namespace PHPOrchestra\ApiBundle\Controller;

use Doctrine\Common\Collections\Collection;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DeletedController
 *
 * @Config\Route("deleted")
 */
class DeletedController extends BaseController
{
    /**
     * @Config\Route("/list", name="php_orchestra_api_deleted_list")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return Response
     */
    public function listAction()
    {
        /** @var Collection $nodes */
        $nodes = $this->get('php_orchestra_model.repository.node')->findByDeleted(true);
        $contents = $this->get('php_orchestra_model.repository.content')->findByDeleted(true);

        $deleted = array_merge($nodes, $contents);

        return $this->get('php_orchestra_api.transformer_manager')->get('deleted_collection')->transform($deleted);
    }
}
