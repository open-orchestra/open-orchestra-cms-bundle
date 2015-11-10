<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Tests\AuthorizeStatusChange\Strategies;

use OpenOrchestra\Backoffice\AuthorizeEdition\AreaTemplateStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeTemplatePanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use Phake;

/**
 * Class AreaTemplateStrategyTest
 */
class AreaTemplateStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AreaTemplateStrategy
     */
    protected $areaTemplateStrategy;
    protected $templateRepository;
    protected $authorizationChecker;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $this->templateRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\TemplateRepositoryInterface');
        $this->areaTemplateStrategy = new AreaTemplateStrategy($this->templateRepository,$this->authorizationChecker);

    }

    /**
     * test getName
     */
    public function testGetName()
    {
        $this->assertSame($this->areaTemplateStrategy->getName(),'template');
    }

    /**
     * test support
     *
     * @param StatusableInterface $document
     * @param bool                $expectedResult
     *
     * @dataProvider provideTestSupport
     */
    public function testSupport($document, $expectedResult)
    {
        $this->assertSame($this->areaTemplateStrategy->support($document),$expectedResult);
    }

    /**
     * @return array
     */
    public function provideTestSupport()
    {
        $statusableInterface = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        $areaInterface = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        $templateInterface = Phake::mock('OpenOrchestra\ModelInterface\Model\TemplateInterface');

        return array(
            array($statusableInterface, false),
            array($areaInterface, true),
            array($templateInterface, true)
        );
    }

    /**
     * @param StatusableInterface $document
     * @param bool                $isGrantedUpdateTemplate
     * @param bool                $isGrantedUpdateNode
     * @param bool                $editable
     *
     * @dataProvider provideTestIsEditable
     */
    public function testIsEditable($document,$isGrantedUpdateTemplate, $isGrantedUpdateNode,$editable)
    {
        Phake::when($this->authorizationChecker)->isGranted(TreeTemplatePanelStrategy::ROLE_ACCESS_UPDATE_TEMPLATE)->thenReturn($isGrantedUpdateTemplate);
        Phake::when($this->authorizationChecker)->isGranted(TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE)->thenReturn($isGrantedUpdateNode);
        $this->assertSame($editable, $this->areaTemplateStrategy->isEditable($document));
    }

    /**
     * @return array
     */
    public function provideTestIsEditable()
    {
        $statusableInterface = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        $areaInterface = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        $templateInterface = Phake::mock('OpenOrchestra\ModelInterface\Model\TemplateInterface');

        return array(
            array($statusableInterface,true,true,false),
            array($statusableInterface,true,false,false),
            array($statusableInterface,false,true,false),
            array($statusableInterface,false,false,false),
            array($areaInterface,true,true,true),
            array($areaInterface,true,false,false),
            array($areaInterface,false,true,true),
            array($areaInterface,false,false,false),
            array($templateInterface,true,true,true),
            array($templateInterface,true,false,true),
            array($templateInterface,false,true,false),
            array($templateInterface,false,false,false),
        );
    }
}
