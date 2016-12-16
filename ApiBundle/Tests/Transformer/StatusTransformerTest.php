<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BackofficeBundle\StrategyManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ApiBundle\Transformer\StatusTransformer;

/**
 * Class StatusTransformerTest
 */
class StatusTransformerTest extends AbstractBaseTestCase
{
    protected $facadeClass = 'OpenOrchestra\ApiBundle\Facade\StatusFacade';
    protected $authorizationChecker;
    protected $transformerManager;
    protected $groupContext;
    protected $transformer;
    protected $translator;
    protected $status;
    protected $usageFinder;

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
        $statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        $router = Phake::mock('Symfony\Component\Routing\RouterInterface');

        $statusId = 'StatusId';

        Phake::when($this->status)->getId()->thenReturn($statusId);
        Phake::when($router)->generateRoute(Phake::anyParameters())->thenReturn('route');
        Phake::when($statusRepository)->find(Phake::anyParameters())->thenReturn($this->status);
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
            $this->usageFinder
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
        $this->assertSame($isGranted, $facade->allowed);

        if (!$hasGroup && $isGranted) {
            $this->assertArrayHasKey('_self_delete', $facade->getLinks());
            $this->assertArrayHasKey('_self_form', $facade->getLinks());
        } else {
            $this->isEmpty($facade->getLinks());
        }
    }

    /**
     * @return array
     */
    public function provideTransformData()
    {
        return array(
            array(true, true, true, false),
            array(true, false, true, false),
            array(false, true, true, false),
            array(false, false, true, false),
            array(true, true, false, false),
            array(true, false, false, false),
            array(false, true, false, false),
            array(false, false, false, false),
            array(true, true, true, true),
            array(true, false, true, true),
            array(false, true, true, true),
            array(false, false, true, true),
            array(true, true, false, true),
            array(true, false, false, true),
            array(false, true, false, true),
            array(false, false, false, true),
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
     * Test getName
     */
    public function testGetName()
    {
        $this->assertSame('status', $this->transformer->getName());
    }
}
