<?php
namespace OpenOrchestra\Backoffice\Tests\Reference\Strategies;

use OpenOrchestra\Backoffice\Reference\Strategies\NodeInBlockReferenceStrategy;
use Phake;
use OpenOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Class NodeInBlockReferenceStrategyTest
 */
class NodeInBlockReferenceStrategyTest extends AbstractReferenceStrategyTest
{
    protected $nodeRepository;
    protected $bbcodeParser;
    protected $currentSiteManager;
    /** @var  NodeInBlockReferenceStrategy */
    protected $strategy;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->bbcodeParser = Phake::mock('OpenOrchestra\BBcodeBundle\Parser\BBcodeParserInterface');
        $this->currentSiteManager = Phake::mock('OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface');
        $linkTag = Phake::mock('OpenOrchestra\BBcodeBundle\ElementNode\BBcodeElementNodeInterface');
        Phake::when($linkTag)->getAttribute()->thenReturn(array('link'=> '{"label":"link","site_siteId":"2","site_nodeId":"nodeId4"}'));
        Phake::when($this->bbcodeParser)->parse(Phake::anyParameters())->thenReturn($this->bbcodeParser);
        Phake::when($this->bbcodeParser)->getElementByTagName(Phake::anyParameters())->thenReturn(
            array($linkTag)
        );

        $this->strategy = new NodeInBlockReferenceStrategy($this->currentSiteManager, $this->bbcodeParser,  $this->nodeRepository);
    }

    /**
     * provide entity
     *
     * @return array
     */
    public function provideEntity()
    {
        $content = $this->createPhakeContent();
        $block = $this->createPhakeBlock();
        $node = $this->createPhakeNode();
        $contentType = $this->createPhakeContentType();

        return array(
            'Content'      => array($content, false),
            'Block'        => array($block, true),
            'Content Type' => array($contentType, false),
            'Node'         => array($node, false),
        );
    }

    /**
     * @param mixed  $entity
     * @param string $entityId
     * @param array  $nodes
     *
     * @dataProvider provideEntityAndNodes
     */
    public function testAddReferencesToEntity($entity, $entityId, array $nodes)
    {
        $this->strategy->addReferencesToEntity($entity);
        Phake::verify($this->nodeRepository, Phake::times(count($nodes)))->updateUseReference(Phake::anyParameters());
    }

    /**
     * @param mixed  $entity
     * @param string $entityId
     * @param array  $nodes
     *
     * @dataProvider provideEntityAndNodes
     */
    public function testRemoveReferencesToEntity($entity, $entityId, array $nodes)
    {
        parent::checkRemoveReferencesToEntity($entity, $entityId, $nodes, BlockInterface::ENTITY_TYPE, $this->nodeRepository);
    }

    /**
     * @return array
     */
    public function provideEntityAndNodes()
    {
        $blockWithoutNodeId = 'blockWithoutNodeId';
        $blockWithoutNode = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($blockWithoutNode)->getAttributes()->thenReturn(array());
        Phake::when($blockWithoutNode)->getId()->thenReturn($blockWithoutNodeId);

        $nodeId = 'nodeId';
        $node1 = $this->createPhakeNode($nodeId);
        $blockWithNode1Id = 'blockId1';
        $blockWithNode1 = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($blockWithNode1)->getAttributes()
            ->thenReturn(array(
                    'nodeToLink' => $nodeId
                )
            );
        Phake::when($blockWithNode1)->getId()->thenReturn($blockWithNode1Id);

        $nodeId2 = 'nodeId2';
        $node2 = $this->createPhakeNode($nodeId2);
        $blockWithNode2Id = 'blockId2';
        $blockWithNode2 = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($blockWithNode2)->getAttributes()
            ->thenReturn(array(
                    'nodeName' => $nodeId2
                )
            );
        Phake::when($blockWithNode2)->getId()->thenReturn($blockWithNode2Id);

        $nodeId3 = 'nodeId3';
        $node3 = $this->createPhakeNode($nodeId3);
        $blockWithNode3Id = 'blockId3';
        $blockWithNode3 = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($blockWithNode3)->getAttributes()
            ->thenReturn(array(
                    'contentNodeId' => $nodeId3
                )
            );
        Phake::when($blockWithNode3)->getId()->thenReturn($blockWithNode3Id);

        $blockWithNodeTinymceId = 'blockIdTinymce';
        $nodeId4 = 'nodeId4';
        $node4 = $this->createPhakeNode($nodeId4);
        $blockWithNodeTinymce = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        $bbCodeWithLink = 'Some [b]String[b] with [link={"label":"link","site_siteId":"2","site_nodeId":"nodeId4"}]link[/link]';
        Phake::when($blockWithNodeTinymce)->getAttributes()->thenReturn(array($bbCodeWithLink));
        Phake::when($blockWithNodeTinymce)->getId()->thenReturn($blockWithNodeTinymceId);

        return array(
            'Block without node' => array($blockWithoutNode, $blockWithoutNodeId, array()),
            'Block with node (node to link)' => array($blockWithNode1, $blockWithNode1Id, array($nodeId => $node1)),
            'Block with node (nodeName)' => array($blockWithNode2, $blockWithNode2Id, array($nodeId2 => $node2)),
            'Block with node (contentNodeId)' => array($blockWithNode3, $blockWithNode3Id, array($nodeId3 => $node3)),
            'Block with node (tinymce)' => array($blockWithNodeTinymce, $blockWithNodeTinymceId, array($nodeId4 => $node4)),
        );
    }
}
