<?php

namespace PHPOrchestra\LogBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use PHPOrchestra\ModelInterface\Event\KeywordEvent;
use PHPOrchestra\ModelInterface\KeywordEvents;
use Symfony\Bridge\Monolog\Logger;

/**
 * Class LogKeywordSubscriber
 */
class LogKeywordSubscriber implements EventSubscriberInterface
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
     * @param KeywordEvent $event
     */
    public function keywordCreate(KeywordEvent $event)
    {
        $keyword = $event->getKeyword();
        $this->logger->info('Create a new keyword', array('keyword_label' => $keyword->getLabel()));
    }

    /**
     * @param KeywordEvent $event
     */
    public function keywordDelete(KeywordEvent $event)
    {
        $keyword = $event->getKeyword();
        $this->logger->info('Delete a keyword', array('keyword_label' => $keyword->getLabel()));
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KeywordEvents::KEYWORD_CREATE => 'keywordCreate',
            KeywordEvents::KEYWORD_DELETE => 'keywordDelete',
        );
    }
}
