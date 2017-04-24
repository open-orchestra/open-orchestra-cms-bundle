<?php

namespace OpenOrchestra\GroupBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use OpenOrchestra\GroupBundle\Document\Perimeter;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\Backoffice\Repository\GroupRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;

/**
 * Class GroupPerimeterSubscriber
 */
class GroupPerimeterSubscriber implements EventSubscriberInterface
{
    protected $groupRepository;
    protected $siteRepository;

    /**
     * @param GroupRepositoryInterface $groupRepository
     */
    public function __construct(GroupRepositoryInterface $groupRepository, SiteRepositoryInterface $siteRepository)
    {
        $this->groupRepository = $groupRepository;
        $this->siteRepository = $siteRepository;
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        if (($group = $event->getData()) instanceof GroupInterface) {
            $isSiteAdmin = $group->hasRole(ContributionRoleInterface::SITE_ADMIN);
            $siteId = $group->getSite()->getSiteId();

            $perimeter = new Perimeter();
            $perimeter->setType(SiteInterface::ENTITY_TYPE);
            $items = !is_null($group->getPerimeter(SiteInterface::ENTITY_TYPE)) ? $group->getPerimeter(SiteInterface::ENTITY_TYPE)->getItems() : array();

            foreach ($items as $item) {
                if ($item != $siteId) {
                    $perimeter->addItem($item);
                }
            }
            if ($isSiteAdmin) {
                $perimeter->addItem($siteId);
            }
            $group->addPerimeter($perimeter);
        }
    }

    /**
     * @param NodeEvent $event
     */
    public function updateNodeInPerimeter(NodeEvent $event)
    {
        $node = $event->getNode();
        $site = $this->siteRepository->findOneBySiteId($node->getSiteId());

        $this->groupRepository->updatePerimeterItem(
            NodeInterface::ENTITY_TYPE,
            $event->getPreviousPath(),
            $node->getPath(),
            $site->getId()
        );
    }

    /**
     * @param NodeEvent $event
     */
    public function removeNodeFromPerimeter(NodeEvent $event)
    {
        $node = $event->getNode();

        $site = $this->siteRepository->findOneBySiteId($node->getSiteId());

        $this->groupRepository->removePerimeterItem(
            NodeInterface::ENTITY_TYPE,
            $node->getPath(),
            $site->getId()
        );
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SUBMIT  => 'postSubmit',
            NodeEvents::PATH_UPDATED => 'updateNodeInPerimeter',
            NodeEvents::NODE_REMOVED => 'removeNodeFromPerimeter',
        );
    }
}
