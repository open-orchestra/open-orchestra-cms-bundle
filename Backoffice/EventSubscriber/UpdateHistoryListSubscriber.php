<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\Model\HistorisableInterface;

/**
 * Class UpdateHistoryListSubscriber
 */
class UpdateHistoryListSubscriber implements EventSubscriberInterface
{
    protected $tokenManager;
    protected $objectManager;
    protected $historyClass;

    /**
     * @param TokenStorageInterface $tokenManager
     * @param ObjectManager         $objectManager
     * @param string                $historyClass
     */
    public function __construct(TokenStorageInterface $tokenManager, ObjectManager $objectManager, $historyClass)
    {
        $this->tokenManager = $tokenManager;
        $this->objectManager = $objectManager;
        $this->historyClass = $historyClass;
    }

    /**
     * @param ContentEvent $event
     */
    public function addHistory(ContentEvent $event)
    {
        $document = $event->getContent();
        $token = $this->tokenManager->getToken();
        if ($document instanceof HistorisableInterface && !is_null($token)) {
            $user = $token->getUser();
            $historyClass = $this->historyClass;
            $history = new $historyClass();
            $history->setUpdatedAt(new \DateTime());
            $history->setUser($user);
            $document->addHistory($history);
            $this->objectManager->flush();
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ContentEvents::CONTENT_UPDATE => 'addHistory',
            ContentEvents::CONTENT_CREATION => 'addHistory',
            ContentEvents::CONTENT_DELETE => 'addHistory',
            ContentEvents::CONTENT_RESTORE => 'addHistory',
            ContentEvents::CONTENT_DUPLICATE => 'addHistory',
            ContentEvents::CONTENT_CHANGE_STATUS => 'addHistory',
        );
    }
}
