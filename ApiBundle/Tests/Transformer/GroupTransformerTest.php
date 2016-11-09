<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

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
    protected $multiLanguagesChoiceManager;
    protected $eventDispatcher;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $this->multiLanguagesChoiceManager = Phake::mock('OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface');
        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        Phake::when($this->multiLanguagesChoiceManager)->choose(Phake::anyParameters())->thenReturn('foo');

        $this->transformerInterface = Phake::mock('OpenOrchestra\ApiBundle\Transformer\TransformerWithGroupInterface');
        Phake::when($this->transformerInterface)->transform(Phake::anyParameters())->thenReturn(Phake::mock('OpenOrchestra\BaseApi\Facade\FacadeInterface'));
        $this->router = Phake::mock('Symfony\Component\Routing\RouterInterface');
        $this->context = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        Phake::when($this->context)->getRouter()->thenReturn($this->router);
        Phake::when($this->context)->get(Phake::anyParameters())->thenReturn($this->transformerInterface);

        $groupContext = Phake::mock('OpenOrchestra\BaseApi\Context\GroupContext');
        Phake::when($this->context)->getGroupContext()->thenReturn($groupContext);
        Phake::when($groupContext)->hasGroup(Phake::anyParameters())->thenReturn(true);

        $this->transformer = new GroupTransformer(
            $this->facadeClass,
            $this->authorizationChecker,
            $this->multiLanguagesChoiceManager,
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
        $this->expectException('OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException');
        $this->transformer->transform(Phake::mock('stdClass'));
    }

    /**
     * @param bool $right
     *
     * @dataProvider provideRights
     */
    public function testTransform($right)
    {
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn($right);

        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($group)->getRoles()->thenReturn(array());
        Phake::when($group)->getLabels()->thenReturn(array());

        $facade = $this->transformer->transform($group);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\GroupFacade', $facade);
        if ($right) {
            $this->assertArrayHasKey('_self', $facade->getLinks());
            $this->assertArrayHasKey('_self_delete', $facade->getLinks());
            $this->assertArrayHasKey('_self_form', $facade->getLinks());
            $this->assertArrayHasKey('_self_edit', $facade->getLinks());
            Phake::verify($this->eventDispatcher)->dispatch(Phake::anyParameters());
        }
    }

    /**
     * @return array
     */
    public function provideRights()
    {
        return array(
            array(true),
            array(false),
        );
    }

    /**
     * Test reverse transform with no previous roles
     */
    public function testReverseTransform()
    {
        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\GroupFacade');

        $transformedGroup = $this->transformer->reverseTransform($facade, $group);

        $this->assertSame($group, $transformedGroup);
        Phake::verify($this->eventDispatcher)->dispatch(Phake::anyParameters());
    }
}
