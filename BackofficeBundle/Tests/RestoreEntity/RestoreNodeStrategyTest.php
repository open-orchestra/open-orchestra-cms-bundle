<?php

namespace OpenOrchestra\BackofficeBundle\Tests\RestoreEntity;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\Backoffice\RestoreEntity\Strategies\RestoreNodeStrategy;
use Phake;

/**
 * Class RestoreNodeStrategyTest
 */
class RestoreNodeStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RestoreNodeStrategy
     */
    protected $strategy;

    protected $nodeRepository;
    protected $eventDispatcher;

    /**
     * Set up the test
     */
    protected function setUp()
    {
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $this->strategy = new RestoreNodeStrategy($this->nodeRepository, $this->eventDispatcher);
    }

    /**
     * @param mixed  $entity
     * @param bool   $expected
     *
     * @dataProvider provideSupport
     */
    public function testSupport($entity, $expected)
    {
        $output = $this->strategy->support($entity);
        $this->assertEquals($output, $expected);
    }

    /**
     * @return array
     */
    public function provideSupport()
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');

        return array(
            array($node, true),
            array($content, false),
            array($site, false),
        );
    }

    /**
     * Test restore
     */
    public function testRestore()
    {
        $nodeId = 'nodeId';
        $siteId = 'fakeSiteId';
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getNodeId()->thenReturn($nodeId);
        Phake::when($node)->getSiteId()->thenReturn($siteId);
        $nodes = new ArrayCollection();
        $nodes->add($node);
        $nodes->add($node);

        Phake::when($this->nodeRepository)->findByNodeIdAndSiteId($nodeId, $siteId)->thenReturn($nodes);

        $this->strategy->restore($node);

        Phake::verify($node, Phake::times(2))->setDeleted(false);
        Phake::verify($this->nodeRepository)->findByNodeIdAndSiteId($nodeId, $siteId);
        Phake::verify($this->eventDispatcher, Phake::times(1))->dispatch(Phake::anyParameters());
    }
}
