<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;

/**
 * Class NodeController
 */
class NodeController extends Controller
{
    /**
     * @param $nodeId
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($nodeId)
    {
        $node = $this->get('php_orchestra_model.repository.node')->findOneByNodeId($nodeId);

        return $this->get('php_orchestra_api.transformer_manager')->get('node')->transform($node);
    }
}
 