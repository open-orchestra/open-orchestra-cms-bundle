<?php

namespace OpenOrchestra\LogBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\KeywordEvent;
use OpenOrchestra\ModelInterface\KeywordEvents;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;

/**
 * Class LogKeywordSubscriber
 */
class LogKeywordSubscriber extends AbstractLogSubscriber
{
    /**
     * @param KeywordEvent $event
     */
    public function keywordCreate(KeywordEvent $event)
    {
        $this->sendLog('open_orchestra_log.keyword.create', $event->getKeyword());
    }

    /**
     * @param KeywordEvent $event
     */
    public function keywordDelete(KeywordEvent $event)
    {
        $this->sendLog('open_orchestra_log.keyword.delete', $event->getKeyword());
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

    /**
     * @param string           $message
     * @param KeywordInterface $keyword
     */
    protected function sendLog($message, KeywordInterface $keyword)
    {
        $this->logger->info($message, array('keyword_label' => $keyword->getLabel()));
    }
}
