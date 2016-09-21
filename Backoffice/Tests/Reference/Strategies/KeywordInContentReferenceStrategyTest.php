<?php
namespace OpenOrchestra\Backoffice\Tests\Reference\Strategies;

use Phake;
use OpenOrchestra\Backoffice\Reference\Strategies\KeywordInContentReferenceStrategy;
use OpenOrchestra\ModelInterface\Model\ContentInterface;

/**
 * Class KeywordInContentStrategyTest
 */
class KeywordInContentStrategyTest extends AbstractReferenceStrategyTest
{
    protected $keywordRepository;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->keywordRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface');

        $this->strategy = new KeywordInContentReferenceStrategy($this->keywordRepository);
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
            'Content'      => array($content, true),
            'Node'         => array($node, false),
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

        parent::checkAddReferencesToEntity($entity, $entityId, $keywords, ContentInterface::ENTITY_TYPE, $this->keywordRepository);
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
        parent::checkRemoveReferencesToEntity($entity, $entityId, $keywords, ContentInterface::ENTITY_TYPE, $this->keywordRepository);
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
            'Content type'              => array($contentType, $contentTypeId, array()),
            'Content with no keyword'   => array($content, $contentId, array()),
            'Content with one keyword'  => array($content, $contentId, array($keyword1Id => $keyword1)),
            'Content with two keywords' => array($content, $contentId, array($keyword2Id => $keyword2, $keyword3Id => $keyword3))
        );
    }
}
