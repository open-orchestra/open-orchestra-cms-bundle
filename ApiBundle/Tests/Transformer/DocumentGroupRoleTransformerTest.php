<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use OpenOrchestra\ApiBundle\Transformer\DocumentGroupRoleTransformer;
use OpenOrchestra\BackofficeBundle\Model\DocumentGroupRoleInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class DocumentGroupRoleTransformerTest
 */
class DocumentGroupRoleTransformerTest extends AbstractBaseTestCase
{
    /**
     * @var DocumentGroupRoleTransformer
     */
    protected $transformer;

    protected $facadeClass = 'OpenOrchestra\ApiBundle\Facade\DocumentGroupRoleFacade';
    protected $context;
    protected $roleCollector;
    protected $documentGroupRoleClass;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->roleCollector = Phake::mock('OpenOrchestra\Backoffice\Collector\RoleCollectorInterface');
        Phake::when($this->roleCollector)->hasRole(Phake::anyParameters())->thenReturn(true);

        $this->documentGroupRoleClass = 'OpenOrchestra\GroupBundle\Document\DocumentGroupRole';
        $this->context = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');

        $this->transformer = new DocumentGroupRoleTransformer(
            $this->facadeClass,
            $this->documentGroupRoleClass,
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
        $this->assertSame('document_group_role', $this->transformer->getName());
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
        $documentGroupRole = Phake::mock('OpenOrchestra\BackofficeBundle\Model\DocumentGroupRoleInterface');
        Phake::when($documentGroupRole)->getType()->thenReturn($type);
        Phake::when($documentGroupRole)->getId()->thenReturn($id);
        Phake::when($documentGroupRole)->getRole()->thenReturn($role);
        Phake::when($documentGroupRole)->getAccessType()->thenReturn($accessType);

        $facade = $this->transformer->transform($documentGroupRole);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\DocumentGroupRoleFacade', $facade);
        $this->assertSame($type, $facade->type);
        $this->assertSame($id, $facade->document);
        $this->assertSame($role, $facade->name);
        $this->assertSame($accessType, $facade->accessType);

    }

    /**
     * @return array
     */
    public function provideTransformData()
    {
        return array(
            array('foo', 'bar', 'baz', DocumentGroupRoleInterface::ACCESS_GRANTED),
            array('baz', 'foo', 'bar', DocumentGroupRoleInterface::ACCESS_DENIED),
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
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\DocumentGroupRoleFacade');
        $facade->type = $type;
        $facade->document = $id;
        $facade->name = $role;
        $facade->accessType = $accessType;
        $group = Phake::mock('OpenOrchestra\BackofficeBundle\Model\GroupInterface');

        $documentGroupRole = $this->transformer->reverseTransformWithGroup($group, $facade);

        $this->assertInstanceOf('OpenOrchestra\BackofficeBundle\Model\DocumentGroupRoleInterface', $documentGroupRole);
        $this->assertSame($type, $documentGroupRole->getType());
        $this->assertSame($id, $documentGroupRole->getId());
        $this->assertSame($role, $documentGroupRole->getRole());
        $this->assertSame($accessType, $documentGroupRole->getAccessType());
        $this->assertSame($granted, $documentGroupRole->isGranted());
    }

    /**
     * @return array
     */
    public function provideReverseTransformData()
    {
        return array(
            array('foo', 'bar', 'baz', DocumentGroupRoleInterface::ACCESS_GRANTED, true),
            array('baz', 'foo', 'bar', DocumentGroupRoleInterface::ACCESS_DENIED, false),
        );
    }

    /**
     * Throw exception when  role not found
     */
    public function testWithNonExistingRole()
    {
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\DocumentGroupRoleFacade');
        $group = Phake::mock('OpenOrchestra\BackofficeBundle\Model\GroupInterface');
        Phake::when($this->roleCollector)->hasRole(Phake::anyParameters())->thenReturn(false);

        $this->setExpectedException('OpenOrchestra\ApiBundle\Exceptions\HttpException\RoleNotFoundHttpException');

        $this->transformer->reverseTransformWithGroup($group, $facade);
    }
}
