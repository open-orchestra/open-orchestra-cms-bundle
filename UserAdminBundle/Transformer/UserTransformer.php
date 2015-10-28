<?php

namespace OpenOrchestra\UserAdminBundle\Transformer;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\UserAdminBundle\Facade\UserFacade;
use OpenOrchestra\UserBundle\Document\User;
use OpenOrchestra\UserAdminBundle\UserFacadeEvents;
use OpenOrchestra\UserAdminBundle\Event\UserFacadeEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class UserTransformer
 */
class UserTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $eventDispatcher;
    protected $translationChoiceManager;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param TranslationChoiceManager $translationChoiceManager
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, TranslationChoiceManager $translationChoiceManager, AuthorizationCheckerInterface $authorizationChecker)
    {
        parent::__construct($authorizationChecker);
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
        $facade = new UserFacade();

        $facade->id = $mixed->getId();
        $facade->username = $mixed->getUsername();
        $facade->roles = implode(',', $mixed->getRoles());

        $groups = $mixed->getGroups();
        $labels = array();
        foreach($groups as $group){
            $labels[] = $this->translationChoiceManager->choose($group->getLabels());
        }

        $facade->groups = implode(',', $labels);

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_user_show',
            array('userId' => $mixed->getId())
        ));
        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_USER)) {
            $facade->addLink('_self_delete', $this->generateRoute(
                'open_orchestra_api_user_delete',
                array('userId' => $mixed->getId())
            ));
        }
        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_USER)) {
            $facade->addLink('_self_form', $this->generateRoute(
                'open_orchestra_user_admin_user_form',
                array('userId' => $mixed->getId())
            ));
        }
        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_USER)) {
            $facade->addLink('_self_panel_password_change', $this->generateRoute(
                'open_orchestra_user_admin_user_change_password',
                array('userId' => $mixed->getId())));
        }
        $this->eventDispatcher->dispatch(
            UserFacadeEvents::POST_USER_TRANSFORMATION,
            new UserFacadeEvent($facade)
        );

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user';
    }

}
