<?php

namespace OpenOrchestra\Backoffice\Tests\Manager;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\Backoffice\Manager\NodePublisher;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\Manager\NodePublisherInterface;
use Phake;

/**
 * Class NodePublisherTest
 */
class NodePublisherTest extends AbstractBaseTestCase
{
    protected $manager;
    protected $statusRepository;
    protected $nodeRepository;
    protected $objectManager;

    protected $node1;
    protected $node1Attributes = array(
        'name' => 'name-1',
        'version' => 'version-1',
        'language' => 'language-1'
    );
    protected $node2;
    protected $node2Attributes = array(
        'name' => 'name-2',
        'version' => 'version-2',
        'language' => 'language-2'
    );
    protected $site;
    protected $siteId = 'site-id';
    protected $fromStatus1;
    protected $fromStatus2;
    protected $publishedStatus;
    protected $unpublishedStatus;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->fromStatus1 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $this->fromStatus2 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $this->publishedStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $this->unpublishedStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
    }

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->site = Phake::mock('OpenOrchestra\ModelInterface\Model\ReadSiteInterface');
        Phake::when($this->site)->getSiteId()->thenReturn($this->siteId);

        $this->node1 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->node1)->getName()->thenReturn($this->node1Attributes['name']);
        Phake::when($this->node1)->getVersion()->thenReturn($this->node1Attributes['version']);
        Phake::when($this->node1)->getLanguage()->thenReturn($this->node1Attributes['language']);

        $this->node2 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->node2)->getName()->thenReturn($this->node2Attributes['name']);
        Phake::when($this->node2)->getVersion()->thenReturn($this->node2Attributes['version']);
        Phake::when($this->node2)->getLanguage()->thenReturn($this->node2Attributes['language']);

        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        Phake::when($this->nodeRepository)->findNodeToAutoPublish(Phake::anyParameters())
            ->thenReturn(array($this->node1, $this->node2));
        Phake::when($this->nodeRepository)->findNodeToAutoUnpublish(Phake::anyParameters())
            ->thenReturn(array($this->node1, $this->node2));

        $this->objectManager = Phake::mock(ObjectManager::CLASS);

        $this->manager = new NodePublisher($this->statusRepository, $this->nodeRepository, $this->objectManager);
    }

    /**
     * Test publishNodes
     *
     * @param mixed $publishedStatus
     * @param mixed $unpublishedStatus
     * @param mixed $expectedReturn
     * @param int   $mustFlush
     *
     * @dataProvider providePublishData
     */
    public function testPublishNodes(array $fromStatus, $publishedStatus, $expectedReturn, $mustFlush)
    {
        Phake::when($this->statusRepository)->findByAutoPublishFrom()->thenReturn($fromStatus);
        Phake::when($this->statusRepository)->findOneByPublished()->thenReturn($publishedStatus);

        $return = $this->manager->publishNodes($this->site);
        Phake::verify($this->objectManager, Phake::times($mustFlush))->flush();
        $this->assertSame($return, $expectedReturn);
    }

    /**
     * provide publish data
     */
    public function providePublishData()
    {
        $fromStatus = array($this->fromStatus1, $this->fromStatus2);

        $fullPublishReturn = array($this->node1Attributes, $this->node2Attributes);

        return array(
            'NO_PUBLISH_FROM' => array(
                array(), $this->publishedStatus, NodePublisherInterface::ERROR_NO_PUBLISH_FROM_STATUS, 0
            ),
            'NO_PUBLISHED_STATUS' => array(
                $fromStatus, null, NodePublisherInterface::ERROR_NO_PUBLISHED_STATUS, 0
            ),
            'OK' => array(
                $fromStatus, $this->publishedStatus, $fullPublishReturn, 1
            ),
        );
    }

    /**
     * Test unpublishNodes
     *
     * @param mixed $publishedStatus
     * @param mixed $unpublishedStatus
     * @param mixed $expectedReturn
     * @param int   $mustFlush
     *
     * @dataProvider provideUnpublishData
     */
    public function testUnpublishNodes($publishedStatus, $unpublishedStatus, $expectedReturn, $mustFlush)
    {
        Phake::when($this->statusRepository)->findOneByPublished()->thenReturn($publishedStatus);
        Phake::when($this->statusRepository)->findOneByAutoUnpublishTo()->thenReturn($unpublishedStatus);

        $return = $this->manager->unpublishNodes($this->site);
        Phake::verify($this->objectManager, Phake::times($mustFlush))->flush();
        $this->assertSame($return, $expectedReturn);
    }

    /**
     * provide unpublish data
     */
    public function provideUnpublishData()
    {
        $fullUnpublishReturn = array($this->node1Attributes, $this->node2Attributes);

        return array(
            'NO_PUBLISHED_STATUS' => array(
                null, $this->unpublishedStatus, NodePublisherInterface::ERROR_NO_PUBLISHED_STATUS, 0
            ),
            'NO_UNPUBLISHED_STATUS' => array(
                $this->publishedStatus, null, NodePublisherInterface::ERROR_NO_UNPUBLISHED_STATUS, 0
            ),
            'OK' => array(
                $this->publishedStatus, $this->publishedStatus, $fullUnpublishReturn, 1
            ),
        );
    }

}
