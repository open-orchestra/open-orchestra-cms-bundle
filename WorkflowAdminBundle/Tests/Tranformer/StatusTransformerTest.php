<?php

namespace OpenOrchestra\WorkflowAdminBundle\Tests\Transformer;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\WorkflowAdminBundle\Transformer\StatusTransformer;
use OpenOrchestra\ModelInterface\Model\StatusInterface;

/**
 * Class StatusTransformerTest
 */
class StatusTransformerTest extends AbstractBaseTestCase
{
    protected $facadeClass = 'OpenOrchestra\WorkflowAdminBundle\Facade\StatusFacade';
    protected $authorizationChecker;
    protected $transformerManager;
    protected $groupContext;
    protected $transformer;
    protected $translator;
    protected $status;
    protected $usageFinder;
    protected $statusRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $this->groupContext = Phake::mock('OpenOrchestra\BaseApi\Context\GroupContext');
        $multiLanguagesChoiceManager = Phake::mock('OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface');
        $this->status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');

        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $transformerManager = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        $router = Phake::mock('Symfony\Component\Routing\RouterInterface');

        $statusId = 'StatusId';

        Phake::when($this->status)->getId()->thenReturn($statusId);
        Phake::when($router)->generateRoute(Phake::anyParameters())->thenReturn('route');
        Phake::when($this->statusRepository)->find(Phake::anyParameters())->thenReturn($this->status);
        Phake::when($transformerManager)->getGroupContext()->thenReturn($this->groupContext);
        Phake::when($transformerManager)->getRouter()->thenReturn($router);

        $groupContext = Phake::mock('OpenOrchestra\BaseApi\Context\GroupContext');
        Phake::when($transformerManager)->getGroupContext()->thenReturn($groupContext);
        Phake::when($groupContext)->hasGroup(Phake::anyParameters())->thenReturn(true);

        $this->usageFinder = Phake::mock('OpenOrchestra\Backoffice\UsageFinder\StatusUsageFinder');
        Phake::when($this->usageFinder)->hasUsage(Phake::anyParameters())->thenReturn(false);

        $this->transformer = new StatusTransformer(
            $this->facadeClass,
            $multiLanguagesChoiceManager,
            $this->translator,
            $this->authorizationChecker,
            $this->usageFinder,
            $this->statusRepository
        );
        $this->transformer->setContext($transformerManager);
    }

    /**
     * @param bool $publishedState
     * @param bool $initialState
     * @param bool $isGranted
     * @param bool $hasGroup
     *
     * @dataProvider provideTransformData
     */
    public function testTransform($publishedState, $initialState, $isGranted, $hasGroup)
    {
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        $document = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        $attribute = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentAttributeInterface');

        Phake::when($this->authorizationChecker)->isGranted($this->status, $document)->thenReturn($isGranted);
        Phake::when($this->authorizationChecker)->isGranted(ContributionActionInterface::DELETE, $this->status)->thenReturn($isGranted);
        Phake::when($this->authorizationChecker)->isGranted(ContributionActionInterface::EDIT, $this->status)->thenReturn($isGranted);
        Phake::when($this->groupContext)->hasGroup(Phake::anyParameters())->thenReturn($hasGroup);
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn('trans_display_color');
        Phake::when($this->status)->getDisplayColor()->thenReturn('display_color');
        Phake::when($this->status)->isPublishedState()->thenReturn($publishedState);
        Phake::when($this->status)->isInitialState()->thenReturn($initialState);
        Phake::when($this->status)->getLabels()->thenReturn(array());
        Phake::when($content)->getAttributes()->thenReturn(array($attribute, $attribute));

        $facade = $this->transformer->transform($this->status, $document);

        $this->assertInstanceOf('OpenOrchestra\BaseApi\Facade\FacadeInterface', $facade);
        $this->assertSame($publishedState, $facade->publishedState);
        $this->assertSame($initialState, $facade->initialState);

        if (!$hasGroup && $isGranted) {
            $this->assertArrayHasKey('can_delete', $facade->getRights());
        } else {
            $this->isEmpty($facade->getRights());
        }
    }

    /**
     * @return array
     */
    public function provideTransformData()
    {
        return array(
            1  => array(true , true , true , false),
            2  => array(true , false, true , false),
            3  => array(false, true , true , false),
            4  => array(false, false, true , false),
            5  => array(true , true , false, false),
            6  => array(true , false, false, false),
            7  => array(false, true , false, false),
            8  => array(false, false, false, false),
            9  => array(true , true , true , true),
            10 => array(true , false, true , true),
            11 => array(false, true , true , true),
            12 => array(false, false, true , true),
            13 => array(true , true , false, true),
            14 => array(true , false, false, true),
            15 => array(false, true , false, true),
            16 => array(false, false, false, true),
        );
    }

    /**
     * Test Exception transform with wrong object a parameters
     */
    public function testExceptionTransform()
    {
        $this->expectException('OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException');
        $this->transformer->transform(Phake::mock('stdClass'));
    }

    /**
     * test reverseTransform
     *
     * @param string $id
     *
     * @dataProvider provideId
     */
    public function testReverseTransform($id)
    {
        $facade = Phake::mock('OpenOrchestra\BaseApi\Facade\FacadeInterface');
        $facade->id = $id;

        $status = $this->transformer->reverseTransform($facade);

        if (is_null($id)) {
            $this->assertSame(null, $status);
        } else {
            $this->assertSame($this->status, $status);
        }
    }

    /**
     * Provide status id
     *
     * @return array
     */
    public function provideId()
    {
        return array(
            array(null),
            array('fakeId'),
        );
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $this->assertSame('status', $this->transformer->getName());
    }
}
