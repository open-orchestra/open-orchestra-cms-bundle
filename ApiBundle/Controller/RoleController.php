<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RoleController
 *
 * @Config\Route("role")
 */
class RoleController extends Controller
{
    /**
     * @param int $roleId
     *
     * @Config\Route("/{roleId}", name="php_orchestra_api_role_show")
     * @Config\Method({"GET"})
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($roleId)
    {
        $role = $this->get('php_orchestra_model.repository.role')->find($roleId);

        return $this->get('php_orchestra_api.transformer_manager')->get('role')->transform($role);
    }

    /**
     * @Config\Route("", name="php_orchestra_api_role_list")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $roleCollection = $this->get('php_orchestra_model.repository.role')->findAll();

        return $this->get('php_orchestra_api.transformer_manager')->get('role_collection')->transform($roleCollection);
    }

    /**
     * @param int $roleId
     *
     * @Config\Route("/{roleId}/delete", name="php_orchestra_api_role_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($roleId)
    {
        $role = $this->get('php_orchestra_model.repository.role')->find($roleId);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->remove($role);
        $dm->flush();

        return new Response('', 200);
    }
}
