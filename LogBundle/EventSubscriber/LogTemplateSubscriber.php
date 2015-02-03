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
        $this->logger->info('Create a new template', array($template->getTemplateId(), $template->getName()));
    }

    /**
     * @param TemplateEvent $event
     */
    public function templateDelete(TemplateEvent $event)
    {
        $template = $event->getTemplate();
        $this->logger->info('Delete a template', array($template->getTemplateId(), $template->getName()));
    }

    /**
     * @param TemplateEvent $event
     */
    public function templateUpdate(TemplateEvent $event)
    {
        $template = $event->getTemplate();
        $this->logger->info('Update a template', array($template->getTemplateId(), $template->getName()));
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            TemplateEvents::TEMPLATE_CREATE => 'templateEvents',
            TemplateEvents::TEMPLATE_DELETE => 'templateEvents',
            TemplateEvents::TEMPLATE_UPDATE => 'templateEvents',
        );
    }

}
