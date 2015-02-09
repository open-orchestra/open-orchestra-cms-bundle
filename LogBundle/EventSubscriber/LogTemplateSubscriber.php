<?php

namespace PHPOrchestra\LogBundle\EventSubscriber;

use PHPOrchestra\ModelInterface\Event\TemplateEvent;
use PHPOrchestra\ModelInterface\TemplateEvents;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LogTemplateSubscriber
 */
class LogTemplateSubscriber implements EventSubscriberInterface
{
    protected $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param TemplateEvent $event
     */
    public function templateCreate(TemplateEvent $event)
    {
        $template = $event->getTemplate();
        $this->logger->info('php_orchestra_log.template.create', array(
            'template_id' => $template->getTemplateId(),
            'template_name' => $template->getName()
        ));
    }

    /**
     * @param TemplateEvent $event
     */
    public function templateDelete(TemplateEvent $event)
    {
        $template = $event->getTemplate();
        $this->logger->info('php_orchestra_log.template.delete', array(
            'template_id' => $template->getTemplateId(),
            'template_name' => $template->getName()
        ));
    }

    /**
     * @param TemplateEvent $event
     */
    public function templateUpdate(TemplateEvent $event)
    {
        $template = $event->getTemplate();
        $this->logger->info('php_orchestra_log.template.update', array(
            'template_id' => $template->getTemplateId(),
            'template_name' => $template->getName()
        ));
    }

    /**
     * @param TemplateEvent $event
     */
    public function templateAreaDelete(TemplateEvent $event)
    {
        $template = $event->getTemplate();
        $this->logger->info('php_orchestra_log.template.area.delete', array(
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
        $this->logger->info('php_orchestra_log.template.area.update', array(
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

}
