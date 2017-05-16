<?php

namespace OpenOrchestra\Backoffice\Tests\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\EventListener\UpdateStatusableElementPublished;
use OpenOrchestra\ModelInterface\Event\EventTrait\EventStatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use Phake;
use OpenOrchestra\ModelInterface\Repository\StatusableRepositoryInterface;

/**
 * Class UpdateStatusableElementPublishedTest
 */
class UpdateStatusableElementPublishedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UpdateStatusableElementPublished
     */
    protected $listener;
    protected $repository;
    protected $statusRepository;
    protected $objectManager;
    protected $event;
    protected $unPublishStatus;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->repository = Phake::mock(StatusableRepositoryInterface::CLASS);

        $this->statusRepository = Phake::mock(StatusRepositoryInterface::CLASS);
        $this->unPublishStatus = Phake::mock(StatusInterface::CLASS);
        Phake::when($this->statusRepository)->findOneByAutoUnpublishTo()->thenReturn($this->unPublishStatus);

        $this->objectManager = Phake::mock(ObjectManager::CLASS);
        $this->event = Phake::mock(EventStatusableInterface::CLASS);

        $this->listener = new UpdateStatusableElementPublished(
            $this->repository,
            $this->statusRepository,
            $this->objectManager
        );
    }

    /**
     * Test update status
     *
     * @dataProvider providePublishedStatus
     */
    public function testUpdateStatus($publishedStatus, $countUpdate)
    {
        $statusableElement = Phake::mock(StatusableInterface::CLASS);
        $status = Phake::mock(StatusInterface::CLASS);
        Phake::when($statusableElement)->getStatus()->thenReturn($status);
        Phake::when($status)->isPublishedState()->thenReturn($publishedStatus);
        Phake::when($status)->isOutOfWorkflow()->thenReturn(false);
        Phake::when($this->event)->getStatusableElement()->thenReturn($statusableElement);

        $publishedElements = Phake::mock(StatusableInterface::CLASS);
        Phake::when($this->repository)->findPublished($statusableElement)->thenReturn(array($publishedElements));

        $this->listener->updateStatus($this->event);

        Phake::verify($this->statusRepository, Phake::times($countUpdate))->findOneByAutoUnpublishTo();
        Phake::verify($publishedElements, Phake::times($countUpdate))->setStatus($this->unPublishStatus);
    }

    /**
     * @return array
     */
    public function providePublishedStatus()
    {
        return array(
            array(true, 1),
            array(false, 0),
        );
    }
}
