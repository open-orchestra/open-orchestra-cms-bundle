<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Tests\AuthorizeStatusChange\Strategies;

use OpenOrchestra\Backoffice\AuthorizeEdition\TemplateStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeTemplatePanelStrategy;
use Phake;

/**
 * Class TemplateStrategyTest
 */
class TemplateStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TemplateStrategy
     */
    protected $templateStrategy;
    protected $templateRepository;
    protected $authorizationChecker;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $this->templateRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\TemplateRepositoryInterface');
        $this->templateStrategy = new TemplateStrategy($this->templateRepository,$this->authorizationChecker);
    }

    /**
     * test getName
     */
    public function testGetName()
    {
        $this->assertSame($this->templateStrategy->getName(),'template');
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
        $this->assertSame($this->templateStrategy->support($document),$expectedResult);
    }

    /**
     * @return array
     */
    public function provideTestSupport()
    {
        $statusableInterface = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        $templateInterface = Phake::mock('OpenOrchestra\ModelInterface\Model\TemplateInterface');

        return array(
            array($statusableInterface, false),
            array($templateInterface, true)
        );
    }

    /**
     * @param StatusableInterface $document
     * @param bool                $isGrantedUpdateTemplate
     * @param bool                $editable
     *
     * @dataProvider provideTestIsEditable
     */
    public function testIsEditable($document,$isGrantedUpdateTemplate, $editable)
    {
        Phake::when($this->authorizationChecker)->isGranted(TreeTemplatePanelStrategy::ROLE_ACCESS_UPDATE_TEMPLATE)->thenReturn($isGrantedUpdateTemplate);
        $this->assertSame($editable, $this->templateStrategy->isEditable($document));
    }

    /**
     * @return array
     */
    public function provideTestIsEditable()
    {
        $statusableInterface = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        $templateInterface = Phake::mock('OpenOrchestra\ModelInterface\Model\TemplateInterface');

        return array(
            array($statusableInterface,true,false),
            array($statusableInterface,false,false),
            array($templateInterface,true,true),
            array($templateInterface,false,false),
        );
    }
}
