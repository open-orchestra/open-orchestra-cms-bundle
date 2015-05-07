<?php

namespace OpenOrchestra\UserAdminBundle\Controller\Api;

use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\UserBundle\Event\UserEvent;
use OpenOrchestra\UserBundle\UserEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 *
 * @Config\Route("user")
 */
class UserController extends BaseController
{
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
     * @Config\Route("", name="open_orchestra_api_user_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_USER')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $userCollection = $this->get('open_orchestra_user.repository.user')->findAll();

        return $this->get('open_orchestra_api.transformer_manager')->get('user_collection')->transform($userCollection);
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
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $this->dispatchEvent(UserEvents::USER_DELETE, new UserEvent($user));
        $dm->remove($user);
        $dm->flush();

        return new Response('', 200);
    }
}
