<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use OpenOrchestra\ApiBundle\Transformer\ModelGroupRoleTransformer;
use OpenOrchestra\Backoffice\Model\ModelGroupRoleInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class ModelGroupRoleTransformerTest
 */
class ModelGroupRoleTransformerTest extends AbstractBaseTestCase
{
    /**
     * @var ModelGroupRoleTransformer
     */
    protected $transformer;

    protected $facadeClass = 'OpenOrchestra\ApiBundle\Facade\ModelGroupRoleFacade';
    protected $context;
    protected $roleCollector;
    protected $modelGroupRoleClass;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->roleCollector = Phake::mock('OpenOrchestra\Backoffice\Collector\RoleCollectorInterface');
        Phake::when($this->roleCollector)->hasRole(Phake::anyParameters())->thenReturn(true);

        $this->modelGroupRoleClass = 'OpenOrchestra\GroupBundle\Document\ModelGroupRole';
        $this->context = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');

        $this->transformer = new ModelGroupRoleTransformer(
            $this->facadeClass,
            $this->modelGroupRoleClass,
            $this->roleCollector
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
        $this->assertSame('model_group_role', $this->transformer->getName());
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
     * @param string $type
     * @param string $id
     * @param string $role
     * @param string $accessType
     *
     * @dataProvider provideTransformData
     */
    public function testTransform($type, $id, $role, $accessType)
    {
        $modelGroupRole = Phake::mock('OpenOrchestra\Backoffice\Model\ModelGroupRoleInterface');
        Phake::when($modelGroupRole)->getType()->thenReturn($type);
        Phake::when($modelGroupRole)->getId()->thenReturn($id);
        Phake::when($modelGroupRole)->getRole()->thenReturn($role);
        Phake::when($modelGroupRole)->getAccessType()->thenReturn($accessType);

        $facade = $this->transformer->transform($modelGroupRole);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\ModelGroupRoleFacade', $facade);
        $this->assertSame($type, $facade->type);
        $this->assertSame($id, $facade->modelId);
        $this->assertSame($role, $facade->name);
        $this->assertSame($accessType, $facade->accessType);

    }

    /**
     * @return array
     */
    public function provideTransformData()
    {
        return array(
            array('foo', 'bar', 'baz', ModelGroupRoleInterface::ACCESS_GRANTED),
            array('baz', 'foo', 'bar', ModelGroupRoleInterface::ACCESS_DENIED),
        );
    }

    /**
     * @param string $type
     * @param string $id
     * @param string $role
     * @param string $accessType
     * @param bool   $granted
     *
     * @dataProvider provideReverseTransformData
     */
    public function testReverseTransformGroupWithNoExistingData($type, $id, $role, $accessType, $granted)
    {
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\ModelGroupRoleFacade');
        $facade->type = $type;
        $facade->modelId = $id;
        $facade->name = $role;
        $facade->accessType = $accessType;
        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');

        $modelGroupRole = $this->transformer->reverseTransformWithGroup($group, $facade);

        $this->assertInstanceOf('OpenOrchestra\Backoffice\Model\ModelGroupRoleInterface', $modelGroupRole);
        $this->assertSame($type, $modelGroupRole->getType());
        $this->assertSame($id, $modelGroupRole->getId());
        $this->assertSame($role, $modelGroupRole->getRole());
        $this->assertSame($accessType, $modelGroupRole->getAccessType());
        $this->assertSame($granted, $modelGroupRole->isGranted());
    }

    /**
     * @return array
     */
    public function provideReverseTransformData()
    {
        return array(
            array('foo', 'bar', 'baz', ModelGroupRoleInterface::ACCESS_GRANTED, true),
            array('baz', 'foo', 'bar', ModelGroupRoleInterface::ACCESS_DENIED, false),
        );
    }

    /**
     * Throw exception when  role not found
     */
    public function testWithNonExistingRole()
    {
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\ModelGroupRoleFacade');
        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($this->roleCollector)->hasRole(Phake::anyParameters())->thenReturn(false);

        $this->setExpectedException('OpenOrchestra\ApiBundle\Exceptions\HttpException\RoleNotFoundHttpException');

        $this->transformer->reverseTransformWithGroup($group, $facade);
    }
}
