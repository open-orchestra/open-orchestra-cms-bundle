<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ModelInterface\ContentTypeEvents;
use OpenOrchestra\ModelInterface\Event\ContentTypeEvent;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ContentTypeOrderFieldSubscriber
 */
class ContentTypeOrderFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @param ContentTypeEvent $event
     */
    public function orderFields(ContentTypeEvent $event)
    {
        $contentType = $event->getContentType();
        $fields = $contentType->getFields()->toArray();
        uasort($fields, function (FieldTypeInterface $field1, FieldTypeInterface $field2) {
            return $field1->getPosition() >= $field2->getPosition() ? 1 : -1;
        });
        $fields = array_values($fields);
        $contentType->setFields(new ArrayCollection($fields));
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ContentTypeEvents::CONTENT_TYPE_PRE_PERSIST => 'orderFields',
        );
    }
}
