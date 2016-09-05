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
     * @param mixed $entity
     * @param array $keywords
     *
     * @dataProvider provideEntityAndKeywords
     */
    public function testAddReferencesToEntity($entity, array $keywords)
    {
        Phake::when($entity)->getKeywords()->thenReturn($keywords);

        parent::checkAddReferencesToEntity($entity, $keywords, ContentInterface::ENTITY_TYPE, $this->keywordRepository);
    }

    /**
     * @param mixed $entity
     * @param array $keywords
     *
     * @dataProvider provideEntityAndKeywords
     */
    public function testRemoveReferencesToEntity($entity, array $keywords)
    {
        parent::checkRemoveReferencesToEntity($entity, $keywords, ContentInterface::ENTITY_TYPE, $this->keywordRepository);
    }

    /**
     * @return array
     */
    public function provideEntityAndKeywords()
    {
        $node = $this->createPhakeNode();
        $content = $this->createPhakeContent();
        $contentType = $this->createPhakeContentType();

        $keyword1Id = 'keyword1';
        $keyword2Id = 'keyword2';
        $keyword3Id = 'keyword3';

        $keyword1 = $this->createPhakeKeyword($keyword1Id);
        $keyword2 = $this->createPhakeKeyword($keyword2Id);
        $keyword3 = $this->createPhakeKeyword($keyword3Id);

        return array(
            'Node'                      => array($node, array()),
            'Content type'              => array($contentType, array()),
            'Content with no keyword'   => array($content, array()),
            'Content with one keyword'  => array($content, array($keyword1Id => $keyword1)),
            'Content with two keywords' => array($content, array($keyword2Id => $keyword2, $keyword3Id => $keyword3))
        );
    }
}
