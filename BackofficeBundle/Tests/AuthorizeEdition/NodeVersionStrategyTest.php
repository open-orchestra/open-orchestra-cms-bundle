<?php

namespace OpenOrchestra\BackofficeBundle\Tests\AuthorizeEdition;

use OpenOrchestra\Backoffice\AuthorizeEdition\NodeVersionStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\GeneralNodesPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Phake;

/**
 * Test NodeVersionStrategyTest
 */
class NodeVersionStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeVersionStrategy
     */
    protected $strategy;

    protected $repository;
    protected $lastVersionNode;
    protected $authorizationChecker;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->lastVersionNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $this->repository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        Phake::when($this->repository)
            ->findInLastVersion(Phake::anyParameters())
            ->thenReturn($this->lastVersionNode);

        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');

        $this->strategy = new NodeVersionStrategy($this->repository, $this->authorizationChecker);
    }

    /**
     * Test implementation
     */
    public function testInstance()
    {
        $this->assertInstanceOf('OpenOrchestra\Backoffice\AuthorizeEdition\AuthorizeEditionInterface', $this->strategy);
    }

    /**
     * @param string $document
     * @param bool   $support
     *
     * @dataProvider provideDocumentAndSupport
     */
    public function testSupport($document, $support)
    {
        $document = Phake::mock($document);

        $this->assertSame($support, $this->strategy->support($document));
    }

    /**
     * @return array
     */
    public function provideDocumentAndSupport()
    {
        return array(
            array('OpenOrchestra\ModelInterface\Model\StatusableInterface', false),
            array('OpenOrchestra\ModelInterface\Model\ContentInterface', false),
            array('OpenOrchestra\ModelInterface\Model\NodeInterface', true),
            array('stdClass', false),
        );
    }

    /**
     * @param int     $nodeVersion
     * @param int     $lastNodeVersion
     * @param string  $nodeType
     * @param bool    $isGranted
     * @param bool    $editable
     *
     * @dataProvider provideVersionAndEditable
     */
    public function testIsEditable($nodeVersion, $lastNodeVersion, $nodeType, $isGranted, $editable)
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getVersion()->thenReturn($nodeVersion);

        Phake::when($this->authorizationChecker)->isGranted(TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE)->thenReturn($isGranted);

        Phake::when($this->authorizationChecker)->isGranted(GeneralNodesPanelStrategy::ROLE_ACCESS_UPDATE_GENERAL_NODE)->thenReturn($isGranted);

        Phake::when($this->lastVersionNode)->getVersion()->thenReturn($lastNodeVersion);

        Phake::when($node)->getNodeType()->thenReturn($nodeType);

        $this->assertSame($editable, $this->strategy->isEditable($node));
    }

    /**
     * @return array
     */
    public function provideVersionAndEditable()
    {
        return array(
            array(1, 2, NodeInterface::TYPE_DEFAULT, false, false),
            array(2, 2, NodeInterface::TYPE_DEFAULT, true, true),
            array(3, 2, NodeInterface::TYPE_DEFAULT, true, true),
            array(3, 2, NodeInterface::TYPE_DEFAULT, false, false),
            array(1, 2, NodeInterface::TYPE_TRANSVERSE, false, false),
            array(2, 2, NodeInterface::TYPE_TRANSVERSE, true, true),
            array(3, 2, NodeInterface::TYPE_TRANSVERSE, true, true),
            array(3, 2, NodeInterface::TYPE_TRANSVERSE, false, false),
        );
    }

    /**
     * Test with no node
     */
    public function testIsEditableWithNoNode()
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->repository)->findInLastVersion(Phake::anyParameters())->thenReturn(null);
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn(true);
        $this->assertSame(true, $this->strategy->isEditable($node));
    }
}
