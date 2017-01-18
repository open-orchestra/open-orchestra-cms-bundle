<?php
namespace OpenOrchestra\Backoffice\Tests\Reference\Strategies;

use OpenOrchestra\Backoffice\Reference\Strategies\KeywordInNodeReferenceStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Phake;

/**
 * Class KeywordInNodeReferenceStrategyTest
 */
class KeywordInNodeReferenceStrategyTest extends AbstractReferenceStrategyTest
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
     * @param string $entityId
     * @param array  $keywords
     *
     * @dataProvider provideEntityAndKeywords
     */
    public function testAddReferencesToEntity($entity, $entityId, array $keywords)
    {
        Phake::when($entity)->getKeywords()->thenReturn($keywords);

        parent::checkAddReferencesToEntity($entity, $entityId, $keywords, NodeInterface::ENTITY_TYPE, $this->keywordRepository);
    }

    /**
     * @param mixed  $entity
     * @param string $entityId
     * @param array  $keywords
     *
     * @dataProvider provideEntityAndKeywords
     */
    public function testRemoveReferencesToEntity($entity, $entityId, array $keywords)
    {
        parent::checkRemoveReferencesToEntity($entity, $entityId, $keywords, NodeInterface::ENTITY_TYPE, $this->keywordRepository);
    }

    /**
     * @return array
     */
    public function provideEntityAndKeywords()
    {
        $nodeId = 'nodeId';
        $node = $this->createPhakeNode($nodeId);
        $contentId = 'contentId';
        $content = $this->createPhakeContent($contentId);
        $contentTypeId = 'contentTypeId';
        $contentType = $this->createPhakeContentType($contentTypeId);

        $keyword1Id = 'keyword1';
        $keyword2Id = 'keyword2';
        $keyword3Id = 'keyword3';

        $keyword1 = $this->createPhakeKeyword($keyword1Id);
        $keyword2 = $this->createPhakeKeyword($keyword2Id);
        $keyword3 = $this->createPhakeKeyword($keyword3Id);

        return array(
            'Node'                      => array($node, $nodeId, array()),
            'Node with one keyword '    => array($node, $nodeId, array($keyword1Id => $keyword1)),
            'Node with two keyword '    => array($node, $nodeId, array($keyword2Id => $keyword2, $keyword3Id => $keyword3)),
            'Content type'              => array($contentType, $contentTypeId, array()),
            'Content'                   => array($content, $contentId, array()),
        );
    }
}
