<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ApiBundle\Transformer\GroupTransformer;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Phake;

/**
 * Test GroupTransformerTest
 */
class GroupTransformerTest extends AbstractBaseTestCase
{
    /**
     * @var GroupTransformer
     */
    protected $transformer;

    protected $facadeClass = 'OpenOrchestra\ApiBundle\Facade\GroupFacade';
    protected $router;
    protected $context;
    protected $transformerInterface;
    protected $authorizationChecker;
    protected $translationChoiceManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $this->translationChoiceManager = Phake::mock('OpenOrchestra\Backoffice\Manager\TranslationChoiceManager');
        Phake::when($this->translationChoiceManager)->choose(Phake::anyParameters())->thenReturn('foo');

        $this->transformerInterface = Phake::mock('OpenOrchestra\ApiBundle\Transformer\TransformerWithGroupInterface');
        Phake::when($this->transformerInterface)->transform(Phake::anyParameters())->thenReturn(Phake::mock('OpenOrchestra\BaseApi\Facade\FacadeInterface'));
        $this->router = Phake::mock('Symfony\Component\Routing\RouterInterface');
        $this->context = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        Phake::when($this->context)->getRouter()->thenReturn($this->router);
        Phake::when($this->context)->get(Phake::anyParameters())->thenReturn($this->transformerInterface);

        $this->transformer = new GroupTransformer($this->facadeClass, $this->authorizationChecker, $this->translationChoiceManager);
        $this->transformer->setContext($this->context);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('group', $this->transformer->getName());
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
     * @param bool $right
     * @param bool $hasSite
     *
     * @dataProvider provideRights
     */
    public function testTransform($right, $hasSite)
    {
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn($right);

        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($group)->getRoles()->thenReturn(array());
        Phake::when($group)->getLabels()->thenReturn(new ArrayCollection());
        Phake::when($group)->getNodeRoles()->thenReturn(array());
        if ($hasSite) {
            $site = Phake::mock('OpenOrchestra\ModelInterface\Model\ReadSiteInterface');
            Phake::when($group)->getSite()->thenReturn($site);
        }

        $facade = $this->transformer->transform($group);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\GroupFacade', $facade);
        if ($right) {
            $this->assertArrayHasKey('_self', $facade->getLinks());
            $this->assertArrayHasKey('_self_delete', $facade->getLinks());
            $this->assertArrayHasKey('_self_form', $facade->getLinks());
            $this->assertArrayHasKey('_self_edit', $facade->getLinks());
            if ($hasSite) {
                $this->assertArrayHasKey('_self_panel_node_tree', $facade->getLinks());
                $this->assertArrayHasKey('_self_node_tree', $facade->getLinks());
                $this->assertArrayHasKey('_role_list_node', $facade->getLinks());
            }
        }
    }

    /**
     * @return array
     */
    public function provideRights()
    {
        return array(
            array(true, true),
            array(false, true),
            array(true, false),
            array(false, false),
        );
    }

    /**
     * Test reverse transform with no previous roles
     */
    public function testReverseTransform()
    {
        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        $nodeGroupRole = Phake::mock('OpenOrchestra\Backoffice\Model\NodeGroupRoleInterface');
        Phake::when($group)->getNodeRoleByNodeAndRole(Phake::anyParameters())->thenReturn($nodeGroupRole);
        Phake::when($this->transformerInterface)->reverseTransformWithGroup(Phake::anyParameters())->thenReturn($nodeGroupRole);

        $nodeGroupRoleFacade = Phake::mock('OpenOrchestra\ApiBundle\Facade\NodeGroupRoleFacade');
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\GroupFacade');
        Phake::when($facade)->getNodeRoles()->thenReturn(array($nodeGroupRoleFacade, $nodeGroupRoleFacade));

        $transformedGroup = $this->transformer->reverseTransform($facade, $group);

        $this->assertSame($group, $transformedGroup);
        Phake::verify($this->transformerInterface, Phake::times(2))->reverseTransformWithGroup($group, $nodeGroupRoleFacade, $nodeGroupRole);
        Phake::verify($group, Phake::times(2))->addNodeRole($nodeGroupRole);
    }

    /**
     * Test reverse transform with previous roles
     */
    public function testReverseTransformWithExistingElement()
    {
        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        $nodeGroupRole = Phake::mock('OpenOrchestra\Backoffice\Model\NodeGroupRoleInterface');
        Phake::when($group)->getNodeRoleByNodeAndRole(Phake::anyParameters())->thenReturn($nodeGroupRole);
        Phake::when($this->transformerInterface)->reverseTransformWithGroup(Phake::anyParameters())->thenReturn($nodeGroupRole);

        $nodeGroupRoleFacade = Phake::mock('OpenOrchestra\ApiBundle\Facade\NodeGroupRoleFacade');
        $nodeGroupRoleFacade->node = NodeInterface::ROOT_NODE_ID;
        $nodeGroupRoleFacade->name = 'FOO_ROLE';
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\GroupFacade');
        Phake::when($facade)->getNodeRoles()->thenReturn(array($nodeGroupRoleFacade, $nodeGroupRoleFacade));

        $transformedGroup = $this->transformer->reverseTransform($facade, $group);

        $this->assertSame($group, $transformedGroup);
        Phake::verify($this->transformerInterface, Phake::times(2))->reverseTransformWithGroup($group, $nodeGroupRoleFacade, $nodeGroupRole);
        Phake::verify($group, Phake::times(2))->addNodeRole($nodeGroupRole);
    }

    /**
     * Test exception reverse transform
     */
    public function testReverseTransformException()
    {
        $transformerInterface = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerInterface');
        Phake::when($this->context)->get(Phake::anyParameters())->thenReturn($transformerInterface);
        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');

        $nodeGroupRoleFacade = Phake::mock('OpenOrchestra\ApiBundle\Facade\NodeGroupRoleFacade');
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\GroupFacade');
        Phake::when($facade)->getNodeRoles()->thenReturn(array($nodeGroupRoleFacade, $nodeGroupRoleFacade));

        $this->setExpectedException('\UnexpectedValueException');
        $this->transformer->reverseTransform($facade, $group);
    }
}
