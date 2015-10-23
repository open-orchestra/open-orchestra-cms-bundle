<?php

namespace OpenOrchestra\BackofficeBundle\Tests\AuthorizeEdition;

use OpenOrchestra\Backoffice\AuthorizeEdition\NodeVersionStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
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
     * @param int  $nodeVersion
     * @param int  $lastNodeVersion
     * @param bool $isGranted
     * @param bool $editable
     *
     * @dataProvider provideVersionAndEditable
     */
    public function testIsEditable($nodeVersion, $lastNodeVersion, $isGranted, $editable)
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getVersion()->thenReturn($nodeVersion);

        Phake::when($this->authorizationChecker)->isGranted(TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE)->thenReturn($isGranted);

        Phake::when($this->lastVersionNode)->getVersion()->thenReturn($lastNodeVersion);

        $this->assertSame($editable, $this->strategy->isEditable($node));
    }

    /**
     * @return array
     */
    public function provideVersionAndEditable()
    {
        return array(
            array(1, 2, false, false),
            array(2, 2, true, true),
            array(3, 2, true, true),
            array(3, 2, false, false),
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
