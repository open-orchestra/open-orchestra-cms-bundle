<?php

namespace OpenOrchestra\BackofficeBundle\Tests\AuthorizeEdition;

use OpenOrchestra\Backoffice\AuthorizeEdition\TransverseNodeEditionRoleStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\GeneralNodesPanelStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Phake;

/**
 * Test TransverseNodeEditionRoleStrategyTest
 */
class TransverseNodeEditionRoleStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TransverseNodeEditionRoleStrategy
     */
    protected $strategy;

    protected $authorizationChecker;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');

        $this->strategy = new TransverseNodeEditionRoleStrategy($this->authorizationChecker);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('OpenOrchestra\Backoffice\AuthorizeEdition\AuthorizeEditionInterface', $this->strategy);
    }

    /**
     * @param string $document
     * @param bool   $support
     *
     * @dataProvider provideDocumentTypeAndSupport
     */
    public function testSupport($document, $type, $support)
    {
        $document = Phake::mock($document);
        if ($type) {
            Phake::when($document)->getNodeType()->thenReturn($type);
        }

        $this->assertSame($support, $this->strategy->support($document));
    }

    /**
     * @return array
     */
    public function provideDocumentTypeAndSupport()
    {
        return array(
            array('OpenOrchestra\ModelInterface\Model\StatusableInterface', false, false),
            array('OpenOrchestra\ModelInterface\Model\ContentInterface', false, false),
            array('stdClass', false, false),
            array('OpenOrchestra\ModelInterface\Model\NodeInterface', NodeInterface::TYPE_DEFAULT, false),
            array('OpenOrchestra\ModelInterface\Model\NodeInterface', NodeInterface::TYPE_TRANSVERSE, true),
            array('OpenOrchestra\ModelInterface\Model\NodeInterface', NodeInterface::TYPE_ERROR, false),
        );
    }

    /**
     * @param bool $isGranted
     * @param bool $expected
     *
     * @dataProvider provideIsGrantedAndAnswer
     */
    public function testIsEditable($isGranted, $expected)
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn($isGranted);

        $this->assertSame($expected, $this->strategy->isEditable($node));
        Phake::verify($this->authorizationChecker)->isGranted(GeneralNodesPanelStrategy::ROLE_ACCESS_UPDATE_GENERAL_NODE, $node);
    }

    /**
     * @return array
     */
    public function provideIsGrantedAndAnswer()
    {
        return array(
            array(true, true),
            array(false, false),
        );
    }
}
