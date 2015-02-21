<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OpenOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class UserController
 *
 * @Config\Route("user")
 */
class UserController extends Controller
{
    /**
     * @param string $userId
     *
     * @Config\Route("/{userId}", name="open_orchestra_api_user_show")
     * @Config\Method({"GET"})
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
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $userCollection = $this->get('open_orchestra_user.repository.user')->findAll();

        return $this->get('open_orchestra_api.transformer_manager')->get('user_collection')->transform($userCollection);
    }
}
