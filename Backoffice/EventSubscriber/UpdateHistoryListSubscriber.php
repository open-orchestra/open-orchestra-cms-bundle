<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
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
    public function addContentHistory(ContentEvent $event)
    {
        $document = $event->getContent();
        $token = $this->tokenManager->getToken();
        if ($document instanceof HistorisableInterface && !is_null($token)) {
            $this->addDocumentHistory($document, $token);
        }
    }

    /**
     * @param NodeEvent $event
     */
    public function addNodeHistory(NodeEvent $event)
    {
        $document = $event->getNode();
        $token = $this->tokenManager->getToken();
        if ($document instanceof HistorisableInterface && !is_null($token)) {
            $this->addDocumentHistory($document, $token);
        }
    }

    /**
     * @param HistorisableInterface $document
     * @param TokenInterface        $token
     */
    protected function addDocumentHistory(HistorisableInterface $document, TokenInterface $token)
    {
        $user = $token->getUser();
        $historyClass = $this->historyClass;
        $history = new $historyClass();
        $history->setUpdatedAt(new \DateTime());
        $history->setUser($user);
        $document->addHistory($history);
        $this->objectManager->flush();
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ContentEvents::CONTENT_UPDATE => 'addContentHistory',
            ContentEvents::CONTENT_CREATION => 'addContentHistory',
            ContentEvents::CONTENT_DELETE => 'addContentHistory',
            ContentEvents::CONTENT_RESTORE => 'addContentHistory',
            ContentEvents::CONTENT_DUPLICATE => 'addContentHistory',
            ContentEvents::CONTENT_CHANGE_STATUS => 'addContentHistory',
            NodeEvents::PATH_UPDATED => 'addNodeHistory',
            NodeEvents::NODE_UPDATE => 'addNodeHistory',
            NodeEvents::NODE_UPDATE_BLOCK => 'addNodeHistory',
            NodeEvents::NODE_UPDATE_BLOCK_POSITION => 'addNodeHistory',
            NodeEvents::NODE_CREATION => 'addNodeHistory',
            NodeEvents::NODE_DELETE => 'addNodeHistory',
            NodeEvents::NODE_RESTORE => 'addNodeHistory',
            NodeEvents::NODE_DUPLICATE => 'addNodeHistory',
            NodeEvents::NODE_ADD_LANGUAGE => 'addNodeHistory',
            NodeEvents::NODE_DELETE_BLOCK => 'addNodeHistory',
            NodeEvents::NODE_DELETE_AREA => 'addNodeHistory',
            NodeEvents::NODE_UPDATE_AREA => 'addNodeHistory',
            NodeEvents::NODE_CHANGE_STATUS => 'addNodeHistory',
        );
    }
}
