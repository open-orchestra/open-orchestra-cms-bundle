<?php

namespace OpenOrchestra\LogBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\TemplateEvent;
use OpenOrchestra\ModelInterface\Event\TemplateFlexEvent;
use OpenOrchestra\ModelInterface\Model\TemplateFlexInterface;
use OpenOrchestra\ModelInterface\TemplateFlexEvents;

/**
 * Class LogTemplateFlexSubscriber
 */
class LogTemplateFlexSubscriber extends AbstractLogSubscriber
{
    /**
     * @param TemplateFlexEvent $event
     */
    public function templateCreate(TemplateFlexEvent $event)
    {
        $this->sendLog('open_orchestra_log.template_flex.create', $event->getTemplate());
    }

    /**
     * @param TemplateFlexEvent $event
     */
    public function templateDelete(TemplateFlexEvent $event)
    {
        $this->sendLog('open_orchestra_log.template_flex.delete', $event->getTemplate());
    }

    /**
     * @param TemplateFlexEvent $event
     */
    public function templateUpdate(TemplateFlexEvent $event)
    {
        $this->sendLog('open_orchestra_log.template_flex.update', $event->getTemplate());
    }

    /**
     * @param TemplateFlexEvent $event
     */
    public function templateAreaDelete(TemplateFlexEvent $event)
    {
        $template = $event->getTemplate();
        $this->logger->info('open_orchestra_log.template_flex.area.delete', array(
            'template_id' => $template->getTemplateId(),
            'template_name' => $template->getName()
        ));
    }

    /**
     * @param TemplateFlexEvent $event
     */
    public function templateAreaUpdate(TemplateFlexEvent $event)
    {
        $template = $event->getTemplate();
        $this->logger->info('open_orchestra_log.template_flex.area.update', array(
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
            TemplateFlexEvents::TEMPLATE_FLEX_CREATE => 'templateCreate',
            TemplateFlexEvents::TEMPLATE_FLEX_DELETE => 'templateDelete',
            TemplateFlexEvents::TEMPLATE_FLEX_UPDATE => 'templateUpdate',
            TemplateFlexEvents::TEMPLATE_FLEX_AREA_DELETE => 'templateAreaDelete',
            TemplateFlexEvents::TEMPLATE_FLEX_AREA_UPDATE => 'templateAreaUpdate',
        );
    }

    /**
     * @param string                $message
     * @param TemplateFlexInterface $template
     */
    protected function sendLog($message, TemplateFlexInterface $template)
    {
        $this->logger->info($message, array(
            'template_id' => $template->getTemplateId(),
            'template_name' => $template->getName()
        ));
    }
}
