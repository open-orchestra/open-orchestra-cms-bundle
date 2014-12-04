<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TagController
 *
 * @Config\Route("tag")
 */
class TagController extends Controller
{
    /**
     * @param int $tagId
     *
     * @Config\Route("/{tagId}", name="php_orchestra_api_tag_show")
     * @Config\Method({"GET"})
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($tagId)
    {
        $tag = $this->get('php_orchestra_model.repository.tag')->find($tagId);

        return $this->get('php_orchestra_api.transformer_manager')->get('tag')->transform($tag);
    }

    /**
     * @Config\Route("", name="php_orchestra_api_tag_list")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $tagCollection = $this->get('php_orchestra_model.repository.tag')->findAll();

        return $this->get('php_orchestra_api.transformer_manager')->get('tag_collection')->transform($tagCollection);
    }

    /**
     * @param int $tagId
     *
     * @Config\Route("/{tagId}/delete", name="php_orchestra_api_tag_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($tagId)
    {
        $tag = $this->get('php_orchestra_model.repository.tag')->find($tagId);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->remove($tag);
        $dm->flush();

        return new Response('', 200);
    }
}
