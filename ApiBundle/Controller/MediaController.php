<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MediaController
 *
 * @Config\Route("media")
 */
class MediaController extends Controller
{
    /**
     * @param int $mediaId
     *
     * @Config\Route("/{mediaId}", name="php_orchestra_api_media_show")
     * @Config\Method({"GET"})
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($mediaId)
    {
        $media = $this->get('php_orchestra_model.repository.media')->find($mediaId);

        return $this->get('php_orchestra_api.transformer_manager')->get('media')->transform($media);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("", name="php_orchestra_api_media_list")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $folderId = $request->get('folder_id');
        $mediaCollection = $this->get('php_orchestra_model.repository.media')->findByFolderId($folderId);

        return $this->get('php_orchestra_api.transformer_manager')->get('media_collection')->transform($mediaCollection);
    }
}
