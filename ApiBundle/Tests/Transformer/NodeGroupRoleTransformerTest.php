<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use OpenOrchestra\ApiBundle\Transformer\NodeGroupRoleTransformer;
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

    protected $context;
    protected $nodeGroupRoleClass;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->nodeGroupRoleClass = 'OpenOrchestra\GroupBundle\Document\NodeGroupRole';

        $this->context = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');

        $this->transformer = new NodeGroupRoleTransformer($this->nodeGroupRoleClass);
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
     * @param bool   $isGranted
     *
     * @dataProvider provideTransformData
     */
    public function testTransform($nodeId, $role, $isGranted)
    {
        $nodeGroupRole = Phake::mock('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface');
        Phake::when($nodeGroupRole)->getNodeId()->thenReturn($nodeId);
        Phake::when($nodeGroupRole)->getRole()->thenReturn($role);
        Phake::when($nodeGroupRole)->isGranted()->thenReturn($isGranted);

        $facade = $this->transformer->transform($nodeGroupRole);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\NodeGroupRoleFacade', $facade);
        $this->assertSame($nodeId, $facade->node);
        $this->assertSame($role, $facade->name);
        $this->assertSame($isGranted, $facade->isGranted);
    }

    /**
     * @return array
     */
    public function provideTransformData()
    {
        return array(
            array('foo', 'bar', true),
            array('bar', 'foo', false),
        );
    }

    /**
     * @param string $node
     * @param string $role
     * @param bool   $isGranted
     *
     * @dataProvider provideTransformData
     */
    public function testReverseTransformWithNoExistingData($node, $role, $isGranted)
    {
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\NodeGroupRoleFacade');
        $facade->node = $node;
        $facade->name = $role;
        $facade->isGranted = $isGranted;

        $nodeGroupRole = $this->transformer->reverseTransform($facade);

        $this->assertInstanceOf('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface', $nodeGroupRole);
        $this->assertSame($node, $nodeGroupRole->getNodeId());
        $this->assertSame($role, $nodeGroupRole->getRole());
        $this->assertSame($isGranted, $nodeGroupRole->isGranted());
    }

    /**
     * @param string $node
     * @param string $role
     * @param bool   $isGranted
     *
     * @dataProvider provideTransformData
     */
    public function testReverseTransformWithExistingData($node, $role, $isGranted)
    {
        $source = Phake::mock('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface');

        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\NodeGroupRoleFacade');
        $facade->node = $node;
        $facade->name = $role;
        $facade->isGranted = $isGranted;

        $nodeGroupRole = $this->transformer->reverseTransform($facade, $source);

        $this->assertInstanceOf('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface', $nodeGroupRole);
        $this->assertSame($source, $nodeGroupRole);
        Phake::verify($source)->setNodeId($node);
        Phake::verify($source)->setRole($role);
        Phake::verify($source)->setGranted($isGranted);
    }
}
