<?php

namespace OpenOrchestra\Backoffice\Tests\AuthorizeEdition;

use OpenOrchestra\Backoffice\AuthorizeEdition\NodeEditionRoleStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Phake;

/**
 * Test NodeEditionRoleStrategyTest
 */
class NodeEditionRoleStrategyTest extends AbstractBaseTestCase
{
    /**
     * @var NodeEditionRoleStrategy
     */
    protected $strategy;

    protected $authorizationChecker;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');

        $this->strategy = new NodeEditionRoleStrategy($this->authorizationChecker);
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
            Phake::when($document)->getId()->thenReturn('fakeId');
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
            array('OpenOrchestra\ModelInterface\Model\NodeInterface', NodeInterface::TYPE_DEFAULT, true),
            array('OpenOrchestra\ModelInterface\Model\NodeInterface', NodeInterface::TYPE_TRANSVERSE, false),
            array('OpenOrchestra\ModelInterface\Model\NodeInterface', NodeInterface::TYPE_ERROR, true),
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
        Phake::verify($this->authorizationChecker)->isGranted(TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, $node);
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
