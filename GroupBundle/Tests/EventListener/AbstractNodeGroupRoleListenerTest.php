<?php

namespace OpenOrchestra\GroupBundle\Tests\EventListener;

use Phake;

/**
 * Class AbstractNodeGroupRoleListenerTest
 */
abstract class AbstractNodeGroupRoleListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $lifecycleEventArgs;
    protected $container;
    protected $nodesRoles = array("access_node", "access_update_node");
    protected $nodeGroupRoleClass = 'OpenOrchestra\GroupBundle\Document\NodeGroupRole';

    /**
     * setUp
     */
    public function setUp()
    {
        $this->lifecycleEventArgs = Phake::mock('Doctrine\ODM\MongoDB\Event\LifecycleEventArgs');
        $this->container = Phake::mock('Symfony\Component\DependencyInjection\Container');
        $roleCollector = Phake::mock('OpenOrchestra\Backoffice\Collector\RoleCollector');
        Phake::when($this->container)->get('open_orchestra_backoffice.collector.role')->thenReturn($roleCollector);
        Phake::when($roleCollector)->getRolesByType(Phake::anyParameters())->thenReturn($this->nodesRoles);
    }

}