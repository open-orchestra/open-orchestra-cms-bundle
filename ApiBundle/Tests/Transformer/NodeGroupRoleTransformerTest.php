<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use OpenOrchestra\ApiBundle\Transformer\NodeGroupRoleTransformer;
use OpenOrchestra\Backoffice\Model\NodeGroupRoleInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
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

    protected $facadeClass = 'OpenOrchestra\ApiBundle\Facade\NodeGroupRoleFacade';
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

        $this->nodeGroupRoleClass = 'OpenOrchestra\GroupBundle\Document\NodeGroupRole';
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
     * Test with wrong element
     */
    public function testTransformWithWrongElement()
    {
        $this->setExpectedException('OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException');
        $this->transformer->transform(Phake::mock('stdClass'));
    }

    /**
     * @param string $nodeId
     * @param string $role
     * @param string $accessType
     *
     * @dataProvider provideTransformData
     */
    public function testTransform($nodeId, $role, $accessType)
    {
        $nodeGroupRole = Phake::mock('OpenOrchestra\Backoffice\Model\NodeGroupRoleInterface');
        Phake::when($nodeGroupRole)->getNodeId()->thenReturn($nodeId);
        Phake::when($nodeGroupRole)->getRole()->thenReturn($role);
        Phake::when($nodeGroupRole)->getAccessType()->thenReturn($accessType);

        $facade = $this->transformer->transform($nodeGroupRole);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\NodeGroupRoleFacade', $facade);
        $this->assertSame($nodeId, $facade->node);
        $this->assertSame($role, $facade->name);
        $this->assertSame($accessType, $facade->accessType);

    }

    /**
     * @return array
     */
    public function provideTransformData()
    {
        return array(
            array('foo', 'bar', NodeGroupRoleInterface::ACCESS_GRANTED),
            array('bar', 'foo', NodeGroupRoleInterface::ACCESS_DENIED),
        );
    }

    /**
     * @param string $node
     * @param string $role
     * @param string $accessType
     * @param bool   $granted
     *
     * @dataProvider provideReverseTransformData
     */
    public function testReverseTransformGroupWithNoExistingData($node, $role, $accessType, $granted)
    {
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\NodeGroupRoleFacade');
        $facade->node = $node;
        $facade->name = $role;
        $facade->accessType = $accessType;
        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($group)->getNodeRoleByNodeAndRole($facade->node, $facade->name)->thenReturn(null);

        $nodeGroupRole = $this->transformer->reverseTransformWithGroup($group, $facade);

        $this->assertInstanceOf('OpenOrchestra\Backoffice\Model\NodeGroupRoleInterface', $nodeGroupRole);
        $this->assertSame($node, $nodeGroupRole->getNodeId());
        $this->assertSame($role, $nodeGroupRole->getRole());
        $this->assertSame($accessType, $nodeGroupRole->getAccessType());
        $this->assertSame($granted, $nodeGroupRole->isGranted());
    }

    /**
     * @return array
     */
    public function provideReverseTransformData()
    {
        return array(
            array('foo', 'bar', NodeGroupRoleInterface::ACCESS_GRANTED, true),
            array('bar', 'foo', NodeGroupRoleInterface::ACCESS_DENIED, false),
        );
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
        $source = Phake::mock('OpenOrchestra\Backoffice\Model\NodeGroupRoleInterface');
        $nodeGroupRoleParent = Phake::mock('OpenOrchestra\Backoffice\Model\NodeGroupRoleInterface');

        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\NodeGroupRoleFacade');
        $facade->node = $nodeId;
        $facade->name = $role;
        $facade->accessType = $accessType;
        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($group)->getNodeRoleByNodeAndRole($facade->node, $facade->name)->thenReturn($source);

        Phake::when($node)->getParentId()->thenReturn('fakeId');
        Phake::when($this->nodeRepository)->findInLastVersion(Phake::anyParameters())->thenReturn($node);
        Phake::when($group)->getNodeRoleByNodeAndRole($node->getParentId(), $facade->name)->thenReturn($nodeGroupRoleParent);
        Phake::when($nodeGroupRoleParent)->isGranted()->thenReturn($parentAccess);

        $nodeGroupRole = $this->transformer->reverseTransformWithGroup($group, $facade, $source);

        $this->assertInstanceOf('OpenOrchestra\Backoffice\Model\NodeGroupRoleInterface', $nodeGroupRole);
        $this->assertSame($source, $nodeGroupRole);
        Phake::verify($source)->setNodeId($nodeId);
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
            array('foo', 'bar', NodeGroupRoleInterface::ACCESS_GRANTED, true, true),
            array('foo', 'bar', NodeGroupRoleInterface::ACCESS_GRANTED, true, false),
            array('bar', 'foo', NodeGroupRoleInterface::ACCESS_DENIED, false, true),
            array('bar', 'foo', NodeGroupRoleInterface::ACCESS_DENIED, false, false),
            array('bar', 'foo', NodeGroupRoleInterface::ACCESS_INHERIT, false, false),
            array('bar', 'foo', NodeGroupRoleInterface::ACCESS_INHERIT, true, true),
        );
    }

    /**
     * Throw exception when  role not found
     */
    public function testWithNonExistingRole()
    {
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\NodeGroupRoleFacade');
        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($this->roleCollector)->hasRole(Phake::anyParameters())->thenReturn(false);

        $this->setExpectedException('OpenOrchestra\ApiBundle\Exceptions\HttpException\RoleNotFoundHttpException');

        $this->transformer->reverseTransformWithGroup($group, $facade);
    }
}
