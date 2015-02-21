<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\RoleEvent;
use OpenOrchestra\ModelInterface\RoleEvents;
use OpenOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RoleController
 *
 * @Config\Route("role")
 */
class RoleController extends BaseController
{
    /**
     * @param int $roleId
     *
     * @Config\Route("/{roleId}", name="open_orchestra_api_role_show")
     * @Config\Method({"GET"})
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($roleId)
    {
        $role = $this->get('open_orchestra_model.repository.role')->find($roleId);

        return $this->get('open_orchestra_api.transformer_manager')->get('role')->transform($role);
    }

    /**
     * @Config\Route("", name="open_orchestra_api_role_list")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $roleCollection = $this->get('open_orchestra_model.repository.role')->findAll();

        return $this->get('open_orchestra_api.transformer_manager')->get('role_collection')->transform($roleCollection);
    }

    /**
     * @param int $roleId
     *
     * @Config\Route("/{roleId}/delete", name="open_orchestra_api_role_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($roleId)
    {
        $role = $this->get('open_orchestra_model.repository.role')->find($roleId);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $this->dispatchEvent(RoleEvents::ROLE_DELETE, new RoleEvent($role));
        $dm->remove($role);
        $dm->flush();

        return new Response('', 200);
    }
}
