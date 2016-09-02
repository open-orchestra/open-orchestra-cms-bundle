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
     * @param mixed $entity
     * @param array $keywords
     *
     * @dataProvider provideEntityAndKeywords
     */
    public function testAddReferencesToEntity($entity, array $keywords)
    {
        Phake::when($entity)->getKeywords()->thenReturn($keywords);

        parent::checkAddReferencesToEntity($entity, $keywords, ContentTypeInterface::ENTITY_TYPE, $this->keywordRepository);
    }

    /**
     * @param mixed $entity
     * @param array $keywords
     *
     * @dataProvider provideEntityAndKeywords
     */
    public function testRemoveReferencesToEntity($entity, array $keywords)
    {
        parent::checkRemoveReferencesToEntity($entity, $keywords, ContentTypeInterface::ENTITY_TYPE, $this->keywordRepository);
    }

    /**
     * @return array
     */
    public function provideEntityAndKeywords()
    {
        $node = $this->createPhakeNode();
        $content = $this->createPhakeContent();

        $keywordId = 'keyword';
        $keyword = $this->createPhakeKeyword($keywordId);

        $optionWithoutKeyword = Phake::Mock('OpenOrchestra\ModelInterface\Model\FieldOptionInterface');
        Phake::when($optionWithoutKeyword)->getValue()->thenReturn(array());

        $optionWithKeyword = Phake::Mock('OpenOrchestra\ModelInterface\Model\FieldOptionInterface');
        Phake::when($optionWithKeyword)->getValue()->thenReturn(array('keywords' => 'keyword AND fake'));

        $contentTypeWithoutKeyword = $this->createPhakeContentType();
        $fieldWithoutKeyword = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($fieldWithoutKeyword)->getOptions()->thenReturn(array($optionWithoutKeyword));
        Phake::when($contentTypeWithoutKeyword)->getFields()->thenReturn(array($fieldWithoutKeyword));

        $contentTypeWithKeyword = $this->createPhakeContentType();
        $fieldWithKeyword = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($fieldWithKeyword)->getOptions()->thenReturn(array($optionWithoutKeyword, $optionWithKeyword));
        Phake::when($contentTypeWithKeyword)->getFields()->thenReturn(array($fieldWithoutKeyword, $fieldWithKeyword));

        return array(
            'Node'                           => array($node, array()),
            'Content'                        => array($content, array()),
            'Content type with no keyword'   => array($contentTypeWithoutKeyword, array()),
            'Content type with one keyword'  => array($contentTypeWithKeyword, array($keywordId => $keyword))
        );
    }
}
