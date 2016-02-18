<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\GroupBundle\Event\GroupFacadeEvent;
use OpenOrchestra\GroupBundle\GroupFacadeEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;

/**
 * Class GroupTransformer
 */
class GroupTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $eventDispatcher;
    protected $translationChoiceManager;

    /**
     * @param string                        $facadeClass
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TranslationChoiceManager      $translationChoiceManager
     * @param EventDispatcherInterface      $eventDispatcher
     */
    public function __construct(
        $facadeClass,
        AuthorizationCheckerInterface $authorizationChecker,
        TranslationChoiceManager $translationChoiceManager,
        EventDispatcherInterface $eventDispatcher
    ){
        parent::__construct($facadeClass, $authorizationChecker);
        $this->translationChoiceManager = $translationChoiceManager;
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
        $facade->label = $this->translationChoiceManager->choose($group->getLabels());

        foreach ($group->getRoles() as $role) {
            $facade->addRole($role);
        }
        if ($site = $group->getSite()) {
            $facade->site = $this->getTransformer('site')->transform($site);
        }
        foreach ($group->getDocumentRoles() as $documentRoles) {
            $facade->addDocumentRoles($this->getTransformer('document_group_role')->transform($documentRoles));
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
