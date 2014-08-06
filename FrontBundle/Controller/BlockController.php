<?php

namespace PHPOrchestra\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class BlockController
 */
class BlockController extends Controller
{
    /**
     * Display the response linked to a block
     *
     * @param string $nodeId
     * @param string $blockId
     *
     * @Config\Route("/block/{nodeId}/{blockId}", name="php_orchestra_front_block")
     * @Config\Method({"GET"})
     *
     * @throws NotFoundHttpException
     * @return Response
     */
    public function showAction($nodeId, $blockId)
    {
        $node = $this->get('php_orchestra_model.repository.node')->findOneByNodeId($nodeId);

        if (null !== ($block = $node->getBlocks()->get($blockId))) {
            return $this->get('php_orchestra_display.display_block_manager')
                ->show($node->getBlocks()->get($blockId));
        }

        throw new NotFoundHttpException();
    }
}
