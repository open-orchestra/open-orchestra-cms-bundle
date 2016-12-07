<?php

namespace OpenOrchestra\UserAdminBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\UserBundle\Document\User;
use OpenOrchestra\UserAdminBundle\UserFacadeEvents;
use OpenOrchestra\UserAdminBundle\Event\UserFacadeEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class UserTransformer
 */
class UserTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $eventDispatcher;
    protected $multiLanguagesChoiceManager;

    /**
     * @param string                               $facadeClass
     * @param EventDispatcherInterface             $eventDispatcher
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param AuthorizationCheckerInterface        $authorizationChecker
     */
    public function __construct(
        $facadeClass,
        EventDispatcherInterface $eventDispatcher,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        parent::__construct($facadeClass, $authorizationChecker);
        $this->eventDispatcher = $eventDispatcher;
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
    }

    /**
     * @param User $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = $this->newFacade();

        if (!is_null($mixed)) {
            $facade->id = $mixed->getId();
            $facade->username = $mixed->getUsername();
            $facade->roles = implode(',', $mixed->getRoles());

            $groups = $mixed->getGroups();
            $labels = array();
            foreach($groups as $group){
                $labels[] = $this->multiLanguagesChoiceManager->choose($group->getLabels());
            }

            $facade->groups = implode(',', $labels);

            if ($this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $mixed)) {
                $facade->addLink('_self_delete', $this->generateRoute(
                    'open_orchestra_api_user_delete',
                    array('userId' => $mixed->getId())
                ));
            }
            if ($this->authorizationChecker->isGranted(ContributionActionInterface::EDIT, $mixed)) {
                $facade->addLink('_self_form', $this->generateRoute(
                    'open_orchestra_user_admin_user_form',
                    array('userId' => $mixed->getId())
                ));

                $facade->addLink('_self_panel_password_change', $this->generateRoute(
                    'open_orchestra_user_admin_user_change_password',
                    array('userId' => $mixed->getId())));
            }
            $this->eventDispatcher->dispatch(
                UserFacadeEvents::POST_USER_TRANSFORMATION,
                new UserFacadeEvent($facade, $mixed)
            );
        }

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
