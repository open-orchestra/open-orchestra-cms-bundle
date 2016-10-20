<?php
namespace OpenOrchestra\Backoffice\Tests\Reference\Strategies;

use Phake;
use OpenOrchestra\Backoffice\Reference\Strategies\KeywordInBlockReferenceStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Class KeywordInBlockStrategyTest
 */
class KeywordInBlockStrategyTest extends AbstractReferenceStrategyTest
{
    protected $keywordRepository;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->keywordRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface');

        $this->strategy = new KeywordInBlockReferenceStrategy($this->keywordRepository);
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
        $contentType = $this->createPhakeContentType();

        return array(
            'Content'      => array($content, false),
            'Block'         => array($block, true),
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

        parent::checkAddReferencesToEntity($entity, $entityId, $keywords, BlockInterface::ENTITY_TYPE, $this->keywordRepository);
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
        parent::checkRemoveReferencesToEntity($entity, $entityId, $keywords, BlockInterface::ENTITY_TYPE, $this->keywordRepository);
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
        $blockWithoutKeywordId = 'fakeBlockWithoutKeywordId';
        $blockWithKeywordId = 'fakeBlockWithKeywordId';

        $keywordId = 'keyword';
        $keyword = $this->createPhakeKeyword($keywordId);

        $blockWithoutKeyword = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($blockWithoutKeyword)->getAttributes()->thenReturn(array());
        Phake::when($blockWithoutKeyword)->getId()->thenReturn($blockWithoutKeywordId);

        $blockWithKeyword = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($blockWithKeyword)->getAttributes()->thenReturn(array('keywords' => 'keyword AND fake'));
        Phake::when($blockWithKeyword)->getId()->thenReturn($blockWithKeywordId);

        return array(
            'Content'              => array($content, $contentId, array()),
            'Content type'         => array($contentType, $contentTypeId, array()),
            'Block with no keyword' => array($blockWithoutKeyword, $blockWithoutKeywordId, array()),
            'Block with keyword'    => array($blockWithKeyword, $blockWithKeywordId, array($keywordId => $keyword)),
        );
    }
}
