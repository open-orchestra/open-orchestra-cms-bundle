<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\Model\ReportableInterface;

/**
 * Class UpdateReportListSubscriber
 */
class UpdateReportListSubscriber implements EventSubscriberInterface
{
    protected $tokenManager;
    protected $objectManager;
    protected $reportClass;

    /**
     * @param TokenStorageInterface $tokenManager
     * @param ObjectManager         $objectManager
     * @param string                $reportClass
     */
    public function __construct(TokenStorageInterface $tokenManager, ObjectManager $objectManager, $reportClass)
    {
        $this->tokenManager = $tokenManager;
        $this->objectManager = $objectManager;
        $this->reportClass = $reportClass;
    }

    /**
     * @param NodeEvent $event
     */
    public function addReport(ContentEvent $event)
    {
        $document = $event->getContent();
        $token = $this->tokenManager->getToken();
        if ($document instanceof ReportableInterface && !is_null($token)) {
            $user = $token->getUser();
            $reportClass = $this->reportClass;
            $report = new $reportClass();
            $report->setUpdatedAt(new \DateTime());
            $report->setUser($user);
            $document->addReport($report);
            $this->objectManager->flush($document);
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ContentEvents::CONTENT_UPDATE => 'addReport',
            ContentEvents::CONTENT_CREATION => 'addReport',
            ContentEvents::CONTENT_DELETE => 'addReport',
            ContentEvents::CONTENT_RESTORE => 'addReport',
            ContentEvents::CONTENT_DUPLICATE => 'addReport',
            ContentEvents::CONTENT_CHANGE_STATUS => 'addReport',
        );
    }
}
