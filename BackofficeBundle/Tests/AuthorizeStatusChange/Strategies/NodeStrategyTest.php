<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Tests\AuthorizeStatusChange\Strategies;

use OpenOrchestra\Backoffice\AuthorizeStatusChange\Strategies\NodeStrategy;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use Phake;

/**
 * Class NodeStrategyTest
 */
class NodeStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeStrategy
     */
    protected $nodeStrategy;
    protected $authorizationChecker;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $this->nodeStrategy = new NodeStrategy($this->authorizationChecker);

    }

    /**
     * test getName
     */
    public function testGetName()
    {
        $this->assertSame($this->nodeStrategy->getName(),'node');
    }

    /**
     * @param StatusableInterface $document
     * @param bool                $granted
     * @param bool                $expectedResult
     *
     * @dataProvider provideStatusableEvent
     */
    public function testIsGranted(StatusableInterface $document, $granted, $expectedResult)
    {
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn($granted);
        $toStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $this->assertEquals($expectedResult, $this->nodeStrategy->isGranted($document, $toStatus));
    }

    /**
     * @return array
     */
    public function provideStatusableEvent()
    {
        $statusableInterface = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        $nodeInterface = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

        return array(
            array($statusableInterface, true, true),
            array($nodeInterface, false, false),
            array($nodeInterface, true, true)
        );
    }
}
