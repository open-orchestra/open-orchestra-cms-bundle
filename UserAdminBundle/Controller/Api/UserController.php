<?php

namespace OpenOrchestra\UserAdminBundle\Controller\Api;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\HandleRequestDataTable;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\UserBundle\Event\UserEvent;
use OpenOrchestra\UserBundle\UserEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 *
 * @Config\Route("user")
 */
class UserController extends BaseController
{
    use HandleRequestDataTable;

    /**
     * @param string $userId
     *
     * @Config\Route("/{userId}", name="open_orchestra_api_user_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_USER')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($userId)
    {
        $user = $this->get('open_orchestra_user.repository.user')->find($userId);

        return $this->get('open_orchestra_api.transformer_manager')->get('user')->transform($user);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_user_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_USER')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $columnsNameToEntityAttribute = array(
            'username' => array('key' => 'username')
        );

        $repository =  $this->get('open_orchestra_user.repository.user');
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('user_collection');

        return $this->handleRequestDataTable($request, $repository, $columnsNameToEntityAttribute, $collectionTransformer);
    }

    /**
     * @param int $userId
     *
     * @Config\Route("/{userId}/delete", name="open_orchestra_api_user_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_USER')")
     *
     * @return Response
     */
    public function deleteAction($userId)
    {
        $user = $this->get('open_orchestra_user.repository.user')->find($userId);
        $dm = $this->get('object_manager');
        $this->dispatchEvent(UserEvents::USER_DELETE, new UserEvent($user));
        $dm->remove($user);
        $dm->flush();

        return new Response('', 200);
    }
}
