<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Tests\AuthorizeStatusChange\Strategies;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeTemplatePanelStrategy;
use OpenOrchestra\Backoffice\AuthorizeEdition\TemplateStrategy;
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
        $this->templateStrategy = new TemplateStrategy($this->templateRepository, $this->authorizationChecker);
    }

    /**
     * test getName
     */
    public function testGetName()
    {
        $this->assertSame($this->templateStrategy->getName(), 'template');
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
        $document = Phake::mock($document);
        $this->assertSame($this->templateStrategy->support($document), $expectedResult);
    }

    /**
     * @return array
     */
    public function provideTestSupport()
    {
        return array(
            array('OpenOrchestra\ModelInterface\Model\StatusableInterface', false),
            array('OpenOrchestra\ModelInterface\Model\TemplateInterface', true)
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
        $document = Phake::mock('OpenOrchestra\ModelInterface\Model\TemplateInterface');
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn($isGranted);

        $this->assertSame($editable, $this->templateStrategy->isEditable($document));
        Phake::verify($this->authorizationChecker)->isGranted(TreeTemplatePanelStrategy::ROLE_ACCESS_UPDATE_TEMPLATE, $document);
    }

    /**
     * @return array
     */
    public function provideTestIsEditable()
    {
        return array(
            array(true, true),
            array(false, false),
        );
    }
}
