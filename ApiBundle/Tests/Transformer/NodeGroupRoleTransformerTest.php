<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use OpenOrchestra\ApiBundle\Transformer\NodeGroupRoleTransformer;
use OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface;
use Phake;

/**
 * Test NodeGroupRoleTransformerTest
 */
class NodeGroupRoleTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeGroupRoleTransformer
     */
    protected $transformer;

    protected $facadeClass = 'OpenOrchestra\ApiBundle\Facade\NodeGroupRoleFacade';
    protected $context;
    protected $roleCollector;
    protected $nodeGroupRoleClass;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->roleCollector = Phake::mock('OpenOrchestra\Backoffice\Collector\RoleCollector');
        Phake::when($this->roleCollector)->hasRole(Phake::anyParameters())->thenReturn(true);

        $this->nodeGroupRoleClass = 'OpenOrchestra\GroupBundle\Document\NodeGroupRole';

        $this->context = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');

        $this->transformer = new NodeGroupRoleTransformer($this->facadeClass, $this->nodeGroupRoleClass, $this->roleCollector);
        $this->transformer->setContext($this->context);
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
        $nodeGroupRole = Phake::mock('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface');
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
     *
     * @dataProvider provideTransformData
     */
    public function testReverseTransformWithNoExistingData($node, $role, $accessType)
    {
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\NodeGroupRoleFacade');
        $facade->node = $node;
        $facade->name = $role;
        $facade->accessType = $accessType;

        $nodeGroupRole = $this->transformer->reverseTransform($facade);

        $this->assertInstanceOf('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface', $nodeGroupRole);
        $this->assertSame($node, $nodeGroupRole->getNodeId());
        $this->assertSame($role, $nodeGroupRole->getRole());
        $this->assertSame($accessType, $nodeGroupRole->getAccessType());
    }

    /**
     * @param string $node
     * @param string $role
     * @param stro,g $accessType
     *
     * @dataProvider provideTransformData
     */
    public function testReverseTransformWithExistingData($node, $role, $accessType)
    {
        $source = Phake::mock('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface');

        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\NodeGroupRoleFacade');
        $facade->node = $node;
        $facade->name = $role;
        $facade->accessType = $accessType;

        $nodeGroupRole = $this->transformer->reverseTransform($facade, $source);

        $this->assertInstanceOf('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface', $nodeGroupRole);
        $this->assertSame($source, $nodeGroupRole);
        Phake::verify($source)->setNodeId($node);
        Phake::verify($source)->setRole($role);
        Phake::verify($source)->setAccessType($accessType);
    }

    /**
     * Throw exception when  role not found
     */
    public function testWithNonExistingRole()
    {
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\NodeGroupRoleFacade');
        Phake::when($this->roleCollector)->hasRole(Phake::anyParameters())->thenReturn(false);

        $this->setExpectedException('OpenOrchestra\ApiBundle\Exceptions\HttpException\RoleNotFoundHttpException');

        $this->transformer->reverseTransform($facade);
    }
}
