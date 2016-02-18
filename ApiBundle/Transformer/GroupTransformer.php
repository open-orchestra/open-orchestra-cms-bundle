<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BackofficeBundle\Model\DocumentGroupRoleInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\GroupBundle\Event\GroupFacadeEvent;
use OpenOrchestra\GroupBundle\GroupFacadeEvents;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use UnexpectedValueException;

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
            $facade->addLink('_self_panel_node_tree', $this->generateRoute(
                'open_orchestra_api_group_show',
                array('groupId' => $group->getId())
            ));
            if ($group->getSite() instanceof ReadSiteInterface) {
                $facade->addLink('_self_node_tree', $this->generateRoute(
                    'open_orchestra_api_node_list_tree',
                    array('siteId' => $group->getSite()->getSiteId())
                ));
                $facade->addLink('_role_list_node', $this->generateRoute(
                    'open_orchestra_api_role_list_by_type',
                    array('type' => 'node')
                ));
            }
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
        $transformer = $this->getTransformer('node_group_role');
        if (!$transformer instanceof TransformerWithGroupInterface) {
            throw new UnexpectedValueException("Document Group Role Transformer must be an instance of TransformerWithContextInterface");
        }
        foreach ($facade->getDocumentRoles() as $documentRoleFacade) {
            if (DocumentGroupRoleInterface::TYPE_NODE === $documentRoleFacade->type) {
                $source = $group->getDocumentRoleByTypeAndIdAndRole(
                    $documentRoleFacade->type,
                    $documentRoleFacade->document,
                    $documentRoleFacade->name
                );
                $documentGroupRole = $transformer->reverseTransformWithGroup($group, $documentRoleFacade, $source);
                $group->addDocumentRole($documentGroupRole);
            }
        }

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
