<?php

namespace OpenOrchestra\GroupBundle\Tests\Transformer;

use OpenOrchestra\GroupBundle\Transformer\GroupTransformer;
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

    protected $facadeClass = 'OpenOrchestra\GroupBundle\Facade\GroupFacade';
    protected $router;
    protected $context;
    protected $transformerInterface;
    protected $authorizationChecker;
    protected $multiLanguagesChoiceManager;
    protected $groupRepository;
    protected $businessRulesManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $this->multiLanguagesChoiceManager = Phake::mock('OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface');
        $this->groupRepository = Phake::mock('OpenOrchestra\Backoffice\Repository\GroupRepositoryInterface');
        $this->businessRulesManager = Phake::mock('OpenOrchestra\Backoffice\BusinessRules\BusinessRulesManager');

        Phake::when($this->multiLanguagesChoiceManager)->choose(Phake::anyParameters())->thenReturn('foo');
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn(true);

        $this->transformerInterface = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerInterface');
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
            $this->groupRepository,
            $this->businessRulesManager
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

        $this->assertInstanceOf('OpenOrchestra\GroupBundle\Facade\GroupFacade', $facade);
        if ($right) {
            $this->assertArrayHasKey('can_duplicate', $facade->getRights());
            $this->assertArrayHasKey('can_delete', $facade->getRights());
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
        $facade = Phake::mock('OpenOrchestra\GroupBundle\Facade\GroupFacade');
        $transformedGroup = $this->transformer->reverseTransform($facade);

        $this->assertEquals(null, $transformedGroup);
    }
}
