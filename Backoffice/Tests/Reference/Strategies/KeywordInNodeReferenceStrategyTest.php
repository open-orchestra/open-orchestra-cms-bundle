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
     * @param mixed $entity
     * @param array $keywords
     *
     * @dataProvider provideEntityAndKeywords
     */
    public function testAddReferencesToEntity($entity, array $keywords)
    {
        Phake::when($entity)->getKeywords()->thenReturn($keywords);

        parent::checkAddReferencesToEntity($entity, $keywords, NodeInterface::ENTITY_TYPE, $this->keywordRepository);
    }

    /**
     * @param mixed $entity
     * @param array $keywords
     *
     * @dataProvider provideEntityAndKeywords
     */
    public function testRemoveReferencesToEntity($entity, array $keywords)
    {
        parent::checkRemoveReferencesToEntity($entity, $keywords, NodeInterface::ENTITY_TYPE, $this->keywordRepository);
    }

    /**
     * @return array
     */
    public function provideEntityAndKeywords()
    {
        $content = $this->createPhakeContent();
        $contentType = $this->createPhakeContentType();

        $keywordId = 'keyword';
        $keyword = $this->createPhakeKeyword($keywordId);

        $blockWithoutKeyword = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($blockWithoutKeyword)->getAttributes()->thenReturn(array());

        $blockWithKeyword = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($blockWithKeyword)->getAttributes()->thenReturn(array('keywords' => 'keyword AND fake'));

        $nodeWithoutKeyword = $this->createPhakeNode('NodeNoKeyword');
        Phake::when($nodeWithoutKeyword)->getBlocks()->thenReturn(array($blockWithoutKeyword));

        $nodeWithKeyword = $this->createPhakeNode('NodeWithKeyword');
        Phake::when($nodeWithKeyword)->getBlocks()->thenReturn(array($blockWithoutKeyword, $blockWithKeyword));

        return array(
            'Content'              => array($content, array()),
            'Content type'         => array($contentType, array()),
            'Node with no keyword' => array($nodeWithoutKeyword, array()),
            'Node with keyword'    => array($nodeWithKeyword, array($keywordId => $keyword)),
        );
    }
}
