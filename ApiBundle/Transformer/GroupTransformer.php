<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
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
    protected $translationChoiceManager;

    /**
     * @param string                        $facadeClass
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TranslationChoiceManager      $translationChoiceManager
     */
    public function __construct(
        $facadeClass,
        AuthorizationCheckerInterface $authorizationChecker,
        TranslationChoiceManager $translationChoiceManager
    ){
        parent::__construct($facadeClass, $authorizationChecker);
        $this->translationChoiceManager = $translationChoiceManager;
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
        foreach ($group->getNodeRoles() as $nodeRole) {
            $facade->addNodeRoles($this->getTransformer('node_group_role')->transform($nodeRole));
        }
        foreach ($group->getMediaFolderRoles() as $mediaFolderRole) {
            $facade->addMediaFolderRoles($this->getTransformer('media_folder_group_role')->transform($mediaFolderRole));
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
            $facade->addLink('_self_panel_media_folder_tree', $this->generateRoute(
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
                $facade->addLink('_self_folder_tree', $this->generateRoute(
                    'open_orchestra_api_folder_list_tree',
                    array('siteId' => $group->getSite()->getSiteId())
                ));
                $facade->addLink('_role_list_media_folder', $this->generateRoute(
                    'open_orchestra_api_role_list_by_type',
                    array('type' => 'media_folder')
                ));
            }
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
            throw new UnexpectedValueException("Node Group Role Transformer must be an instance of TransformerWithContextInterface");
        }
        foreach ($facade->getNodeRoles() as $nodeRoleFacade) {
            $group->addNodeRole($transformer->reverseTransformWithGroup(
                $group,
                $nodeRoleFacade,
                $group->getNodeRoleByNodeAndRole($nodeRoleFacade->node, $nodeRoleFacade->name)
                )
            );
        }

        $mediaFolderTransformer = $this->getTransformer('media_folder_group_role');
        if (!$mediaFolderTransformer instanceof TransformerWithGroupInterface) {
            throw new UnexpectedValueException("Media Folder Group Role Transformer must be an instance of TransformerWithContextInterface");
        }
        foreach ($facade->getMediaFolderRoles() as $mediaFolderRoleFacade) {
            $group->addMediaFolderRole($mediaFolderTransformer->reverseTransformWithGroup(
                $group,
                $mediaFolderRoleFacade,
                $group->getNodeRoleByNodeAndRole($mediaFolderRoleFacade->folder, $mediaFolderRoleFacade->name)
                )
            );
        }

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
