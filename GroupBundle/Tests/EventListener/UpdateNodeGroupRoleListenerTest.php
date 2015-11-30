<?php

namespace OpenOrchestra\GroupBundle\Tests\EventListener;

use OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface;
use OpenOrchestra\GroupBundle\EventListener\UpdateNodeGroupRoleListener;
use Phake;

/**
 * Class UpdateNodeGroupRoleListenerTest
 */
class UpdateNodeGroupRoleListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $listener;
    protected $lifecycleEventArgs;
    protected $documentManager;
    protected $uow;
    protected $group;
    protected $nodeGroupRoleChild;
    protected $nodeClass = 'OpenOrchestra\ModelBundle\Document\Node';

    /**
     * setUp
     */
    public function setUp()
    {
        $this->lifecycleEventArgs = Phake::mock('Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs');
        $this->documentManager = Phake::mock('Doctrine\ODM\MongoDB\DocumentManager');
        $this->uow = Phake::mock('Doctrine\ODM\MongoDB\UnitOfWork');

        Phake::when($this->lifecycleEventArgs)->getDocumentManager()->thenReturn($this->documentManager);
        Phake::when($this->documentManager)->getUnitOfWork()->thenReturn($this->uow);
        $nodeGroupRole = Phake::mock('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface');
        $childNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $this->group = Phake::mock('OpenOrchestra\BackofficeBundle\Model\GroupInterface');
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        $nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->nodeGroupRoleChild = Phake::mock('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface');

        Phake::when($this->lifecycleEventArgs)->getDocument()->thenReturn($nodeGroupRole);
        Phake::when($this->uow)->getParentAssociation($nodeGroupRole)->thenReturn(array(array(), $this->group));
        Phake::when($this->group)->getSite()->thenReturn($site);
        Phake::when($this->documentManager)->getRepository(Phake::anyParameters())->thenReturn($nodeRepository);
        Phake::when($nodeRepository)->findByParent(Phake::anyParameters())->thenReturn(array($childNode));
        Phake::when($this->lifecycleEventArgs)->hasChangedField(Phake::anyParameters())->thenReturn(true);

        $this->listener = new UpdateNodeGroupRoleListener($this->nodeClass);
    }

    /**
     * @param string  $childAccessType
     * @param integer $countUpdateChildGroupRole
     *
     * @dataProvider provideNodeGroupRoleAccess
     */
    public function testPreUpdate($childAccessType, $countUpdateChildGroupRole)
    {
        Phake::when($this->nodeGroupRoleChild)->getAccessType()->thenReturn($childAccessType);
        Phake::when($this->group)->getNodeRoleByNodeAndRole(Phake::anyParameters())->thenReturn($this->nodeGroupRoleChild);

        $this->listener->preUpdate($this->lifecycleEventArgs);

        Phake::verify($this->nodeGroupRoleChild, Phake::times($countUpdateChildGroupRole))->setGranted(Phake::anyParameters());
    }

    /**
     * Test Pre Update Exception
     */
    public function testPreUpdateException()
    {
        Phake::when($this->group)->getNodeRoleByNodeAndRole(Phake::anyParameters())->thenReturn(null);
        $this->setExpectedException('OpenOrchestra\GroupBundle\Exception\NodeGroupRoleNotFoundException');

        $this->listener->preUpdate($this->lifecycleEventArgs);

    }

    /**
     * @return array
     */
    public function provideNodeGroupRoleAccess()
    {
        return array(
            array(NodeGroupRoleInterface::ACCESS_DENIED, 0),
            array(NodeGroupRoleInterface::ACCESS_GRANTED, 0),
            array(NodeGroupRoleInterface::ACCESS_INHERIT, 1),
        );
    }

    /**
     * test if the method is callable
     */
    public function testMethodPrePersistCallable()
    {
        $this->assertTrue(method_exists($this->listener, 'preUpdate'));
    }

}
