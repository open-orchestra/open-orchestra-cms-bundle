<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ApiBundle\Transformer\GroupTransformer;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
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
    protected $eventDispatcher;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $this->translationChoiceManager = Phake::mock('OpenOrchestra\Backoffice\Manager\TranslationChoiceManager');
        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        Phake::when($this->translationChoiceManager)->choose(Phake::anyParameters())->thenReturn('foo');

        $this->transformerInterface = Phake::mock('OpenOrchestra\ApiBundle\Transformer\TransformerWithGroupInterface');
        Phake::when($this->transformerInterface)->transform(Phake::anyParameters())->thenReturn(Phake::mock('OpenOrchestra\BaseApi\Facade\FacadeInterface'));
        $this->router = Phake::mock('Symfony\Component\Routing\RouterInterface');
        $this->context = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        Phake::when($this->context)->getRouter()->thenReturn($this->router);
        Phake::when($this->context)->get(Phake::anyParameters())->thenReturn($this->transformerInterface);

        $this->transformer = new GroupTransformer(
            $this->facadeClass,
            $this->authorizationChecker,
            $this->translationChoiceManager,
            $this->eventDispatcher
        );
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
        Phake::when($group)->getDocumentRoles()->thenReturn(array());
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
            $this->assertArrayHasKey('_self_panel_node_tree', $facade->getLinks());
            Phake::verify($this->eventDispatcher)->dispatch(Phake::anyParameters());
            if ($hasSite) {
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
        $documentGroupRole = Phake::mock('OpenOrchestra\Backoffice\Model\DocumentGroupRoleInterface');
        Phake::when($group)->getNodeRoleByIdAndRole(Phake::anyParameters())->thenReturn($documentGroupRole);
        Phake::when($this->transformerInterface)->reverseTransformWithGroup(Phake::anyParameters())->thenReturn($documentGroupRole);

        $documentGroupRoleFacade = Phake::mock('OpenOrchestra\ApiBundle\Facade\DocumentGroupRoleFacade');
        $documentGroupRoleFacade->type = DocumentGroupRoleInterface::TYPE_NODE;
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\GroupFacade');

        $transformedGroup = $this->transformer->reverseTransform($facade, $group);

        $this->assertSame($group, $transformedGroup);
        Phake::verify($this->eventDispatcher)->dispatch(Phake::anyParameters());
    }

    /**
     * Test reverse transform with previous roles
     */
    public function testReverseTransformWithExistingElement()
    {
        $group = Phake::mock('OpenOrchestra\BackofficeBundle\Model\GroupInterface');
        $documentGroupRole = Phake::mock('OpenOrchestra\BackofficeBundle\Model\DocumentGroupRoleInterface');
        Phake::when($group)->getNodeRoleByIdAndRole(Phake::anyParameters())->thenReturn($documentGroupRole);
        Phake::when($this->transformerInterface)->reverseTransformWithGroup(Phake::anyParameters())->thenReturn($documentGroupRole);

        $documentGroupRoleFacade = Phake::mock('OpenOrchestra\ApiBundle\Facade\DocumentGroupRoleFacade');
        $documentGroupRoleFacade->type = DocumentGroupRoleInterface::TYPE_NODE;
        $documentGroupRoleFacade->document = NodeInterface::ROOT_NODE_ID;
        $documentGroupRoleFacade->name = 'FOO_ROLE';

        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\GroupFacade');
        Phake::when($facade)->getDocumentRoles()->thenReturn(array($documentGroupRoleFacade, $documentGroupRoleFacade));

        $transformedGroup = $this->transformer->reverseTransform($facade, $group);

        $this->assertSame($group, $transformedGroup);
        Phake::verify($this->transformerInterface, Phake::times(2))->reverseTransformWithGroup($group, $documentGroupRoleFacade, $documentGroupRole);
        Phake::verify($group, Phake::times(2))->addDocumentRole($documentGroupRole);
    }

    /**
     * Test exception reverse transform
     */
    public function testReverseTransformException()
    {
        $transformerInterface = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerInterface');
        Phake::when($this->context)->get(Phake::anyParameters())->thenReturn($transformerInterface);
        $group = Phake::mock('OpenOrchestra\BackofficeBundle\Model\GroupInterface');

        $documentGroupRoleFacade = Phake::mock('OpenOrchestra\ApiBundle\Facade\DocumentGroupRoleFacade');
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\GroupFacade');
        Phake::when($facade)->getDocumentRoles()->thenReturn(array($documentGroupRoleFacade, $documentGroupRoleFacade));

        $this->setExpectedException('\UnexpectedValueException');
        $this->transformer->reverseTransform($facade, $group);
    }
}
