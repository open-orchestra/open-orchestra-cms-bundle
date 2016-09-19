<?php
namespace OpenOrchestra\Backoffice\Tests\Reference\Strategies;

use Phake;
use OpenOrchestra\Backoffice\Reference\Strategies\KeywordInContentTypeReferenceStrategy;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;

/**
 * Class KeywordInContentTypeStrategyTest
 */
class KeywordInContentTypetrategyTest extends AbstractReferenceStrategyTest
{
    protected $keywordRepository;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->keywordRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface');

        $this->strategy = new KeywordInContentTypeReferenceStrategy($this->keywordRepository);
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
            'Node'         => array($node, false),
            'Content Type' => array($contentType, true)
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

        parent::checkAddReferencesToEntity($entity, $entityId, $keywords, ContentTypeInterface::ENTITY_TYPE, $this->keywordRepository);
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
        parent::checkRemoveReferencesToEntity($entity, $entityId, $keywords, ContentTypeInterface::ENTITY_TYPE, $this->keywordRepository);
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

        $keywordId = 'keyword';
        $keyword = $this->createPhakeKeyword($keywordId);

        $optionWithoutKeyword = Phake::Mock('OpenOrchestra\ModelInterface\Model\FieldOptionInterface');
        Phake::when($optionWithoutKeyword)->getValue()->thenReturn(array());

        $optionWithKeyword = Phake::Mock('OpenOrchestra\ModelInterface\Model\FieldOptionInterface');
        Phake::when($optionWithKeyword)->getValue()->thenReturn(array('keywords' => 'keyword AND fake'));

        $contentTypeWithoutKeywordId = 'contentTypeWithoutKeywordId';
        $contentTypeWithoutKeyword = $this->createPhakeContentType($contentTypeWithoutKeywordId);
        $fieldWithoutKeyword = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($fieldWithoutKeyword)->getOptions()->thenReturn(array($optionWithoutKeyword));
        Phake::when($contentTypeWithoutKeyword)->getFields()->thenReturn(array($fieldWithoutKeyword));

        $contentTypeWithKeywordId = 'contentTypeWithKeywordId';
        $contentTypeWithKeyword = $this->createPhakeContentType($contentTypeWithKeywordId);
        $fieldWithKeyword = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($fieldWithKeyword)->getOptions()->thenReturn(array($optionWithoutKeyword, $optionWithKeyword));
        Phake::when($contentTypeWithKeyword)->getFields()->thenReturn(array($fieldWithoutKeyword, $fieldWithKeyword));

        return array(
            'Node'                           => array($node, $nodeId, array()),
            'Content'                        => array($content, $contentId, array()),
            'Content type with no keyword'   => array($contentTypeWithoutKeyword, $contentTypeWithoutKeywordId, array()),
            'Content type with one keyword'  => array($contentTypeWithKeyword, $contentTypeWithKeywordId, array($keywordId => $keyword))
        );
    }
}
