<?php

namespace OpenOrchestra\GroupBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use OpenOrchestra\GroupBundle\Document\Perimeter;

/**
 * Class GroupPerimeterSubscriber
 */
class GroupPerimeterSubscriber implements EventSubscriberInterface
{
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
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SUBMIT => 'postSubmit',
        );
    }
}
