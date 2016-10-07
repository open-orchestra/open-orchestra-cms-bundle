<?php

namespace OpenOrchestra\Backoffice\Tests\Reference\Strategies;

use OpenOrchestra\Backoffice\Reference\Strategies\NodeInNodeReferenceStrategy;
use Phake;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class NodeInNodeStrategyTest
 */
class NodeInNodeStrategyTest extends AbstractReferenceStrategyTest
{
    protected $nodeRepository;
    protected $bbcodeParser;
    protected $currentSiteManager;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->bbcodeParser = Phake::mock('OpenOrchestra\BBcodeBundle\Parser\BBcodeParserInterface');
        $this->currentSiteManager = Phake::mock('OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface');
        Phake::when($this->bbcodeParser)->parse(Phake::anyParameters())->thenReturn($this->bbcodeParser);
        Phake::when($this->bbcodeParser)->getElementByTagName(Phake::anyParameters())->thenReturn($this->bbcodeParser);
        $mediaTag = Phake::mock('OpenOrchestra\BBcodeBundle\ElementNode\BBcodeElementNodeInterface');
        Phake::when($mediaTag)->getAttribute()->thenReturn(array('link'=> '{"label":"link","site_siteId":"2","site_nodeId":"nodeId4"}'));
        Phake::when($this->bbcodeParser)->getElementByTagName(Phake::anyParameters())->thenReturn(
            array($mediaTag)
        );

        $this->strategy = new NodeInNodeReferenceStrategy($this->nodeRepository, $this->bbcodeParser, $this->currentSiteManager);
    }

    /**
     * provide entity
     *
     * @return array
     */
    public function provideEntity()
    {
        $content = $this->createPhakeContent();
        $node = $this->createPhakeNode();
        $contentType = $this->createPhakeContentType();

        return array(
            'Content'      => array($content, false),
            'Node'         => array($node, true),
            'Content Type' => array($contentType, false)
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
        parent::checkRemoveReferencesToEntity($entity, $entityId, $nodes, NodeInterface::ENTITY_TYPE, $this->nodeRepository);
    }

    /**
     * @return array
     */
    public function provideEntityAndNodes()
    {
        $blockWithoutNode = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($blockWithoutNode)->getAttributes()->thenReturn(array());

        $nodeWithoutNodeId = 'NodeNoNode';
        $nodeWithoutNode = $this->createPhakeNode($nodeWithoutNodeId);
        Phake::when($nodeWithoutNode)->getBlocks()->thenReturn(array($blockWithoutNode));

        $nodeId = 'nodeId';
        $node = $this->createPhakeNode($nodeId);
        Phake::when($node)->getBlocks()->thenReturn(array());



        $blockWithNode1 = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($blockWithNode1)->getAttributes()
            ->thenReturn(array(
                    'nodeToLink' => $nodeId
                )
            );


        $nodeId2 = 'nodeId2';
        $node2 = $this->createPhakeNode($nodeId2);

        $blockWithNode2 = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($blockWithNode2)->getAttributes()
            ->thenReturn(array(
                    'nodeName' => $nodeId2
                )
            );

        $nodeId3 = 'nodeId3';
        $node3 = $this->createPhakeNode($nodeId3);

        $blockWithNode3 = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($blockWithNode3)->getAttributes()
            ->thenReturn(array(
                    'contentNodeId' => $nodeId3
                )
            );

        $nodeId4 = 'nodeId4';
        $node4 = $this->createPhakeNode($nodeId4);

        $blockWithNodeTinymce = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');

        $bbCodeWithLink = 'Some [b]String[b] with [link={"label":"link","site_siteId":"2","site_nodeId":"nodeId4"}]link[/link]';
        Phake::when($blockWithNodeTinymce)->getAttributes()->thenReturn(array($bbCodeWithLink));

        $nodeWithNodeId = 'NodeWithNode';
        $nodeWithNode = $this->createPhakeNode($nodeWithNodeId);

        Phake::when($nodeWithNode)->getBlocks()->thenReturn(array($blockWithNodeTinymce, $blockWithoutNode, $blockWithNode1, $blockWithNode2, $blockWithNode3));

        return array(
            'Node'              => array($node, $nodeId, array()),
            'Node with no node' => array($blockWithoutNode, $nodeWithoutNodeId, array()),
            'Node with node'    => array($nodeWithNode, $nodeWithNodeId, array($nodeId4 => $node4, $nodeId => $node, $nodeId2 => $node2, $nodeId3 => $node3)),
        );
    }
}
