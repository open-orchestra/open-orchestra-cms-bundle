<?php
namespace OpenOrchestra\Backoffice\Tests\Reference\Strategies;

use Phake;
use OpenOrchestra\Backoffice\Reference\Strategies\KeywordInNodeReferenceStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class KeywordInNodeStrategyTest
 */
class KeywordInNodeStrategyTest extends AbstractReferenceStrategyTest
{
    protected $keywordRepository;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->keywordRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface');

        $this->strategy = new KeywordInNodeReferenceStrategy($this->keywordRepository);
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
     * @param string entityId
     * @param array  $keywords
     *
     * @dataProvider provideEntityAndKeywords
     */
    public function testAddReferencesToEntity($entity, $entityId, array $keywords)
    {
        $this->markTestSkipped();
        Phake::when($entity)->getKeywords()->thenReturn($keywords);

        parent::checkAddReferencesToEntity($entity, $entityId, $keywords, NodeInterface::ENTITY_TYPE, $this->keywordRepository);
    }

    /**
     * @param mixed  $entity
     * @param string entityId
     * @param array  $keywords
     *
     * @dataProvider provideEntityAndKeywords
     */
    public function testRemoveReferencesToEntity($entity, $entityId, array $keywords)
    {
        $this->markTestSkipped();
        parent::checkRemoveReferencesToEntity($entity, $entityId, $keywords, NodeInterface::ENTITY_TYPE, $this->keywordRepository);
    }

    /**
     * @return array
     */
    public function provideEntityAndKeywords()
    {
        $contentId = 'contentId';
        $content = $this->createPhakeContent($contentId);
        $contentTypeId = 'contentTypeId';
        $contentType = $this->createPhakeContentType($contentTypeId);

        $keywordId = 'keyword';
        $keyword = $this->createPhakeKeyword($keywordId);

        $blockWithoutKeyword = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($blockWithoutKeyword)->getAttributes()->thenReturn(array());

        $blockWithKeyword = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($blockWithKeyword)->getAttributes()->thenReturn(array('keywords' => 'keyword AND fake'));

        $nodeWithoutKeywordId = 'NodeNoKeyword';
        $nodeWithoutKeyword = $this->createPhakeNode($nodeWithoutKeywordId);
        Phake::when($nodeWithoutKeyword)->getBlocks()->thenReturn(array($blockWithoutKeyword));

        $nodeWithKeywordId = 'NodeWithKeyword';
        $nodeWithKeyword = $this->createPhakeNode($nodeWithKeywordId);
        Phake::when($nodeWithKeyword)->getBlocks()->thenReturn(array($blockWithoutKeyword, $blockWithKeyword));

        return array(
            'Content'              => array($content, $contentId, array()),
            'Content type'         => array($contentType, $contentTypeId, array()),
            'Node with no keyword' => array($nodeWithoutKeyword, $nodeWithoutKeywordId, array()),
            'Node with keyword'    => array($nodeWithKeyword, $nodeWithKeywordId, array($keywordId => $keyword)),
        );
    }
}
