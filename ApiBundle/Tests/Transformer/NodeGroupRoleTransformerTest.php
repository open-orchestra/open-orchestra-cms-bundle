<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use OpenOrchestra\ApiBundle\Transformer\NodeGroupRoleTransformer;
use OpenOrchestra\Backoffice\Model\ModelGroupRoleInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Phake;

/**
 * Test NodeGroupRoleTransformerTest
 */
class NodeGroupRoleTransformerTest extends AbstractBaseTestCase
{
    /**
     * @var NodeGroupRoleTransformer
     */
    protected $transformer;

    protected $facadeClass = 'OpenOrchestra\ApiBundle\Facade\ModelGroupRoleFacade';
    protected $context;
    protected $roleCollector;
    protected $nodeGroupRoleClass;
    protected $nodeRepository;
    protected $currentSiteManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->roleCollector = Phake::mock('OpenOrchestra\Backoffice\Collector\RoleCollectorInterface');
        Phake::when($this->roleCollector)->hasRole(Phake::anyParameters())->thenReturn(true);

        $this->nodeGroupRoleClass = 'OpenOrchestra\GroupBundle\Document\ModelGroupRole';
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->context = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        $this->currentSiteManager = Phake::mock('OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface');

        $this->transformer = new NodeGroupRoleTransformer(
            $this->facadeClass,
            $this->nodeGroupRoleClass,
            $this->roleCollector,
            $this->nodeRepository,
            $this->currentSiteManager
        );
        $this->transformer->setContext($this->context);
    }

    /**
     * Test interface
     */
    public function testInterface()
    {
        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Transformer\TransformerWithGroupInterface', $this->transformer);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('node_group_role', $this->transformer->getName());
    }

    /**
     * @param string $nodeId
     * @param string $role
     * @param string $accessType
     * @param bool   $parentAccess
     * @param bool   $expectedAccess
     *
     * @dataProvider provideTransformDataWithAccessType
     */
    public function testReverseTransformGroupWitAccessType($nodeId, $role, $accessType, $expectedAccess, $parentAccess)
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $source = Phake::mock('OpenOrchestra\Backoffice\Model\ModelGroupRoleInterface');
        $nodeGroupRoleParent = Phake::mock('OpenOrchestra\Backoffice\Model\ModelGroupRoleInterface');

        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\ModelGroupRoleFacade');
        $facade->type = NodeInterface::GROUP_ROLE_TYPE;
        $facade->modelId = $nodeId;
        $facade->name = $role;
        $facade->accessType = $accessType;
        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($group)->getModelGroupRoleByTypeAndIdAndRole(NodeInterface::GROUP_ROLE_TYPE, $facade->modelId, $facade->name)->thenReturn($source);

        Phake::when($node)->getParentId()->thenReturn('fakeId');
        Phake::when($this->nodeRepository)->findInLastVersion(Phake::anyParameters())->thenReturn($node);
        Phake::when($group)->getModelGroupRoleByTypeAndIdAndRole(NodeInterface::GROUP_ROLE_TYPE, $node->getParentId(), $facade->name)->thenReturn($nodeGroupRoleParent);
        Phake::when($nodeGroupRoleParent)->isGranted()->thenReturn($parentAccess);

        $nodeGroupRole = $this->transformer->reverseTransformWithGroup($group, $facade, $source);

        $this->assertInstanceOf('OpenOrchestra\Backoffice\Model\ModelGroupRoleInterface', $nodeGroupRole);
        $this->assertSame($source, $nodeGroupRole);
        Phake::verify($source)->setType(NodeInterface::GROUP_ROLE_TYPE);
        Phake::verify($source)->setId($nodeId);
        Phake::verify($source)->setRole($role);
        Phake::verify($source)->setAccessType($accessType);
        Phake::verify($source)->setGranted($expectedAccess);
    }

    /**
     * @return array
     */
    public function provideTransformDataWithAccessType()
    {
        return array(
            array('foo', 'bar', ModelGroupRoleInterface::ACCESS_GRANTED, true, true),
            array('foo', 'bar', ModelGroupRoleInterface::ACCESS_GRANTED, true, false),
            array('bar', 'foo', ModelGroupRoleInterface::ACCESS_DENIED, false, true),
            array('bar', 'foo', ModelGroupRoleInterface::ACCESS_DENIED, false, false),
            array('bar', 'foo', ModelGroupRoleInterface::ACCESS_INHERIT, false, false),
            array('bar', 'foo', ModelGroupRoleInterface::ACCESS_INHERIT, true, true),
        );
    }
}
