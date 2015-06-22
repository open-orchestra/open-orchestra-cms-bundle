<?php

namespace OpenOrchestra\LogBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\TemplateEvent;
use OpenOrchestra\ModelInterface\Model\TemplateInterface;
use OpenOrchestra\ModelInterface\TemplateEvents;

/**
 * Class LogTemplateSubscriber
 */
class LogTemplateSubscriber extends AbstractLogSubscriber
{
    /**
     * @param TemplateEvent $event
     */
    public function templateCreate(TemplateEvent $event)
    {
        $this->sendLog('open_orchestra_log.template.create', $event->getTemplate());
    }

    /**
     * @param TemplateEvent $event
     */
    public function templateDelete(TemplateEvent $event)
    {
        $this->sendLog('open_orchestra_log.template.delete', $event->getTemplate());
    }

    /**
     * @param TemplateEvent $event
     */
    public function templateUpdate(TemplateEvent $event)
    {
        $this->sendLog('open_orchestra_log.template.update', $event->getTemplate());
    }

    /**
     * @param TemplateEvent $event
     */
    public function templateAreaDelete(TemplateEvent $event)
    {
        $template = $event->getTemplate();
        $this->logger->info('open_orchestra_log.template.area.delete', array(
            'template_id' => $template->getTemplateId(),
            'template_name' => $template->getName()
        ));
    }

    /**
     * @param TemplateEvent $event
     */
    public function templateAreaUpdate(TemplateEvent $event)
    {
        $template = $event->getTemplate();
        $this->logger->info('open_orchestra_log.template.area.update', array(
            'template_id' => $template->getTemplateId(),
            'template_name' => $template->getName()
        ));
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            TemplateEvents::TEMPLATE_CREATE => 'templateCreate',
            TemplateEvents::TEMPLATE_DELETE => 'templateDelete',
            TemplateEvents::TEMPLATE_UPDATE => 'templateUpdate',
            TemplateEvents::TEMPLATE_AREA_DELETE => 'templateAreaDelete',
            TemplateEvents::TEMPLATE_AREA_UPDATE => 'templateAreaUpdate',
        );
    }

    /**
     * @param string            $message
     * @param TemplateInterface $template
     */
    protected function sendLog($message, TemplateInterface $template)
    {
        $this->logger->info($message, array(
            'template_id' => $template->getTemplateId(),
            'template_name' => $template->getName()
        ));
    }
}
