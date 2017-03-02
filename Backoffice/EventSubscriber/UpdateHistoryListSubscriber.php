<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\Event\NodeDeleteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
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
    public function addContentUpdateHistory(ContentEvent $event)
    {
        $this->addContentHistory($event, ContentEvents::CONTENT_UPDATE);
    }

    /**
     * @param ContentEvent $event
     */
    public function addContentCreationHistory(ContentEvent $event)
    {
        $this->addContentHistory($event, ContentEvents::CONTENT_CREATION);
    }

    /**
     * @param ContentEvent $event
     */
    public function addContentDeleteHistory(ContentEvent $event)
    {
        $this->addContentHistory($event, ContentEvents::CONTENT_DELETE);
    }

    /**
     * @param ContentEvent $event
     */
    public function addContentRestoreHistory(ContentEvent $event)
    {
        $this->addContentHistory($event, ContentEvents::CONTENT_RESTORE);
    }

    /**
     * @param ContentEvent $event
     */
    public function addContentDuplicateHistory(ContentEvent $event)
    {
        $this->addContentHistory($event, ContentEvents::CONTENT_DUPLICATE);
    }

    /**
     * @param ContentEvent $event
     */
    public function addContentChangeStatusHistory(ContentEvent $event)
    {
        $this->addContentHistory($event, ContentEvents::CONTENT_CHANGE_STATUS);
    }

    /**
     * @param NodeEvent $event
     */
    public function addPathUpdatedHistory(NodeEvent $event)
    {
        $this->addNodeHistory($event, NodeEvents::PATH_UPDATED);
    }

    /**
     * @param NodeEvent $event
     */
    public function addNodeUpdateHistory(NodeEvent $event)
    {
        $this->addNodeHistory($event, NodeEvents::NODE_UPDATE);
    }

    /**
     * @param NodeEvent $event
     */
    public function addNodeUpdateBlockHistory(NodeEvent $event)
    {
        $this->addNodeHistory($event, NodeEvents::NODE_UPDATE_BLOCK);
    }

    /**
     * @param NodeEvent $event
     */
    public function addNodeUpdateBlockPositionHistory(NodeEvent $event)
    {
        $this->addNodeHistory($event, NodeEvents::NODE_UPDATE_BLOCK_POSITION);
    }

    /**
     * @param NodeEvent $event
     */
    public function addNodeCreationHistory(NodeEvent $event)
    {
        $this->addNodeHistory($event, NodeEvents::NODE_CREATION);
    }

    /**
     * @param NodeDeleteEvent $event
     */
    public function addNodeDeleteHistory(NodeDeleteEvent $event)
    {
        $this->addNodeHistory($event, NodeEvents::NODE_DELETE);
    }

    /**
     * @param NodeEvent $event
     */
    public function addNodeRestoreHistory(NodeEvent $event)
    {
        $this->addNodeHistory($event, NodeEvents::NODE_RESTORE);
    }

    /**
     * @param NodeEvent $event
     */
    public function addNodeDuplicateHistory(NodeEvent $event)
    {
        $this->addNodeHistory($event, NodeEvents::NODE_DUPLICATE);
    }

    /**
     * @param NodeEvent $event
     */
    public function addNodeAddLanguageHistory(NodeEvent $event)
    {
        $this->addNodeHistory($event, NodeEvents::NODE_ADD_LANGUAGE);
    }

    /**
     * @param NodeEvent $event
     */
    public function addNodeDeleteBlockHistory(NodeEvent $event)
    {
        $this->addNodeHistory($event, NodeEvents::NODE_DELETE_BLOCK);
    }

    /**
     * @param NodeEvent $event
     */
    public function addNodeChangeStatusHistory(NodeEvent $event)
    {
        $this->addNodeHistory($event, NodeEvents::NODE_CHANGE_STATUS);
    }

    /**
     * @param ContentEvent $event
     * @param string       $eventType
     */
    protected function addContentHistory(ContentEvent $event, $eventType)
    {
        $document = $event->getContent();
        $token = $this->tokenManager->getToken();
        if ($document instanceof HistorisableInterface && !is_null($token)) {
            $this->addDocumentHistory($document, $token, $eventType);
        }
    }

    /**
     * @param NodeEvent $event
     * @param string    $eventType
     */
    protected function addNodeHistory(NodeEvent $event, $eventType)
    {
        $document = $event->getNode();
        $token = $this->tokenManager->getToken();
        if ($document instanceof HistorisableInterface && !is_null($token)) {
            $this->addDocumentHistory($document, $token, $eventType);
        }
    }

    /**
     * @param HistorisableInterface $document
     * @param TokenInterface        $token
     * @param string                $eventType
     */
    protected function addDocumentHistory(HistorisableInterface $document, TokenInterface $token, $eventType)
    {
        $user = $token->getUser();
        $historyClass = $this->historyClass;
        $history = new $historyClass();
        $history->setUpdatedAt(new \DateTime());
        $history->setUser($user);
        $history->setEventType($eventType);
        $document->addHistory($history);
        $this->objectManager->flush();
    }

    /**
     * @param TokenInterface $token
     * @param string         $eventType
     */
    protected function createHistoryDocument(TokenInterface $token, $eventType)
    {
        $user = $token->getUser();
        $historyClass = $this->historyClass;
        $history = new $historyClass();
        $history->setUpdatedAt(new \DateTime());
        $history->setUser($user);
        $history->setEventType($eventType);
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ContentEvents::CONTENT_UPDATE => 'addContentUpdateHistory',
            ContentEvents::CONTENT_CREATION => 'addContentCreationHistory',
            ContentEvents::CONTENT_DELETE => 'addContentDeleteHistory',
            ContentEvents::CONTENT_RESTORE => 'addContentRestoreHistory',
            ContentEvents::CONTENT_DUPLICATE => 'addContentDuplicateHistory',
            ContentEvents::CONTENT_CHANGE_STATUS => 'addContentChangeStatusHistory',
            NodeEvents::PATH_UPDATED => 'addPathUpdatedHistory',
            NodeEvents::NODE_UPDATE => 'addNodeUpdateHistory',
            NodeEvents::NODE_UPDATE_BLOCK => 'addNodeUpdateBlockHistory',
            NodeEvents::NODE_UPDATE_BLOCK_POSITION => 'addNodeUpdateBlockPositionHistory',
            NodeEvents::NODE_CREATION => 'addNodeCreationHistory',
            NodeEvents::NODE_RESTORE => 'addNodeRestoreHistory',
            NodeEvents::NODE_DUPLICATE => 'addNodeDuplicateHistory',
            NodeEvents::NODE_ADD_LANGUAGE => 'addNodeAddLanguageHistory',
            NodeEvents::NODE_DELETE_BLOCK => 'addNodeDeleteBlockHistory',
            NodeEvents::NODE_CHANGE_STATUS => 'addNodeChangeStatusHistory',
        );
    }
}
