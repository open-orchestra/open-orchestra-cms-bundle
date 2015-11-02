<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Tests\AuthorizeStatusChange\Strategies;

use OpenOrchestra\Backoffice\AuthorizeEdition\AreaTemplateStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeTemplatePanelStrategy;
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
     * @param bool $isGranted
     * @param bool $editable
     *
     * @dataProvider provideTestIsEditable
     */
    public function testIsEditable($isGranted, $editable)
    {
        $document = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        Phake::when($this->authorizationChecker)->isGranted(TreeTemplatePanelStrategy::ROLE_ACCESS_UPDATE_TEMPLATE)->thenReturn($isGranted);
        $this->assertSame($editable, $this->areaTemplateStrategy->isEditable($document));
    }

    /**
     * @return array
     */
    public function provideTestIsEditable()
    {
        return array(
            array(false, false),
            array(true, true),
        );
    }
}
