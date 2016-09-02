<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\GroupBundle\Event\GroupFacadeEvent;
use OpenOrchestra\GroupBundle\GroupFacadeEvents;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\Model\GroupInterface;

/**
 * Class GroupTransformer
 */
class GroupTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $eventDispatcher;
    protected $multiLanguagesChoiceManager;

    /**
     * @param string                               $facadeClass
     * @param AuthorizationCheckerInterface        $authorizationChecker
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param EventDispatcherInterface             $eventDispatcher
     */
    public function __construct(
        $facadeClass,
        AuthorizationCheckerInterface $authorizationChecker,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        EventDispatcherInterface $eventDispatcher
    ){
        parent::__construct($facadeClass, $authorizationChecker);
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param GroupInterface $group
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($group)
    {
        if (!$group instanceof GroupInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->id = $group->getId();
        $facade->name = $group->getName();
        $facade->label = $this->multiLanguagesChoiceManager->choose($group->getLabels());

        foreach ($group->getRoles() as $role) {
            $facade->addRole($role);
        }
        if ($site = $group->getSite()) {
            $facade->site = $this->getTransformer('site')->transform($site);
        }
        foreach ($group->getModelGroupRoles() as $modelRoles) {
            $facade->addModelRoles($this->getTransformer('model_group_role')->transform($modelRoles));
        }

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_GROUP)) {
            $facade->addLink('_self', $this->generateRoute(
                'open_orchestra_api_group_show',
                array('groupId' => $group->getId())
            ));
        }
        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_GROUP)) {
            $facade->addLink('_self_delete', $this->generateRoute(
                'open_orchestra_api_group_delete',
                array('groupId' => $group->getId())
            ));
        }
        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_GROUP)) {
            $facade->addLink('_self_form', $this->generateRoute(
                'open_orchestra_backoffice_group_form',
                array('groupId' => $group->getId())
            ));
            $facade->addLink('_self_edit', $this->generateRoute(
                'open_orchestra_api_group_edit',
                array('groupId' => $group->getId())
            ));

            $this->eventDispatcher->dispatch(
                GroupFacadeEvents::POST_GROUP_TRANSFORMATION,
                new GroupFacadeEvent($group, $facade)
            );
        }

        return $facade;
    }

    /**
     * @param FacadeInterface     $facade
     * @param GroupInterface|null $group
     *
     * @return mixed
     * @throws
     */
    public function reverseTransform(FacadeInterface $facade, $group = null)
    {
        $this->eventDispatcher->dispatch(
            GroupFacadeEvents::POST_GROUP_REVERSE_TRANSFORMATION,
            new GroupFacadeEvent($group, $facade)
        );

        return $group;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'group';
    }
}
