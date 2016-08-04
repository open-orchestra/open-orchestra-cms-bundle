<?php

namespace OpenOrchestra\UserAdminBundle\Transformer;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\UserBundle\Document\User;
use OpenOrchestra\UserAdminBundle\UserFacadeEvents;
use OpenOrchestra\UserAdminBundle\Event\UserFacadeEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class UserListGroupTransformer
 */
class UserListGroupTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $eventDispatcher;
    protected $translationChoiceManager;

    /**
     * @param string                   $facadeClass
     * @param EventDispatcherInterface $eventDispatcher
     * @param TranslationChoiceManager $translationChoiceManager
     */
    public function __construct(
        $facadeClass,
        EventDispatcherInterface $eventDispatcher,
        TranslationChoiceManager $translationChoiceManager,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        parent::__construct($facadeClass, $authorizationChecker);
        $this->eventDispatcher = $eventDispatcher;
        $this->translationChoiceManager = $translationChoiceManager;
    }

    /**
     * @param User $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = $this->newFacade();

        $facade->firstName = $mixed->getFirstName();
        $facade->lastName = $mixed->getLastName();
        $facade->email = $mixed->getEmail();


      /*  if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_USER)) {
            $facade->addLink('_self_delete', $this->generateRoute(
                'open_orchestra_api_user_delete',
                array('userId' => $mixed->getId())
            ));
        }*/

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user_list_group';
    }

}
