<?php

namespace OpenOrchestra\GroupBundle\EventSubscriber;

use OpenOrchestra\ApiBundle\Transformer\NodeGroupRoleTransformer;
use OpenOrchestra\ApiBundle\Transformer\TransformerWithGroupInterface;
use OpenOrchestra\GroupBundle\Event\GroupFacadeEvent;
use OpenOrchestra\GroupBundle\GroupFacadeEvents;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use UnexpectedValueException;

/**
 * Class NodeGroupRoleTransformSubscriber
 */
class NodeGroupRoleTransformSubscriber implements EventSubscriberInterface
{
    protected $router;
    protected $transformer;

    /**
     * @param UrlGeneratorInterface $router
     * @param NodeGroupRoleTransformer $transformer
     */
    public function __construct(UrlGeneratorInterface $router, NodeGroupRoleTransformer $transformer)
    {
        $this->router = $router;
        $this->transformer = $transformer;
    }

    /**
     * @param GroupFacadeEvent $event
     */
    public function postGroupTransform(GroupFacadeEvent $event)
    {
        $facade = $event->getGroupFacade();
        $group = $event->getGroup();

        $facade->addLink('_self_panel_node_tree', $this->router->generate(
            'open_orchestra_api_group_show',
            array('groupId' => $group->getId()),
            UrlGeneratorInterface::ABSOLUTE_URL
        ));

        if ($group->getSite() instanceof ReadSiteInterface) {
            $facade->addLink('_self_node_tree', $this->router->generate(
                'open_orchestra_api_node_list_tree',
                array('siteId' => $group->getSite()->getSiteId()),
                UrlGeneratorInterface::ABSOLUTE_URL
            ));
            $facade->addLink('_role_list_node', $this->router->generate(
                'open_orchestra_api_role_list_by_type',
                array('type' => 'node'),
                UrlGeneratorInterface::ABSOLUTE_URL
            ));
        }
    }

    /**
     * @param GroupFacadeEvent $event
     *
     * @throws UnexpectedValueException
     */
    public function postGroupReverseTransform(GroupFacadeEvent $event)
    {
        $facade = $event->getGroupFacade();
        $group = $event->getGroup();
        if (!$this->transformer instanceof TransformerWithGroupInterface) {
            throw new UnexpectedValueException("Node Group Role Transformer must be an instance of TransformerWithGroupInterface");
        }
        foreach ($facade->getModelRoles() as $modelGroupRoleFacade) {
            if (NodeInterface::GROUP_ROLE_TYPE === $modelGroupRoleFacade->type) {
                $source = $group->getModelGroupRoleByTypeAndIdAndRole(
                    $modelGroupRoleFacade->type,
                    $modelGroupRoleFacade->modelId,
                    $modelGroupRoleFacade->name
                );
                $modelGroupRole = $this->transformer->reverseTransformWithGroup($group, $modelGroupRoleFacade, $source);
                $group->addModelGroupRole($modelGroupRole);
            }
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            GroupFacadeEvents::POST_GROUP_TRANSFORMATION => 'postGroupTransform',
            GroupFacadeEvents::POST_GROUP_REVERSE_TRANSFORMATION => 'postGroupReverseTransform'
        );
    }
}
