<?php
namespace OpenOrchestra\Backoffice\Tests\Reference\Strategies;

use Phake;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\Backoffice\Reference\Strategies\ContentInNodeReferenceStrategy;

/**
 * Class ContentInNodeStrategyTest
 */
class ContentInNodeStrategyTest extends AbstractReferenceStrategyTest
{
    protected $contentRepository;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');

        $this->strategy = new ContentInNodeReferenceStrategy($this->contentRepository);
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
     * @param array $contents
     *
     * @dataProvider provideEntityAndContents
     */
    public function testAddReferencesToEntity($entity, array $contents)
    {
        Phake::when($this->contentRepository)->findByContentId(Phake::anyParameters())->thenReturn($contents);

        parent::checkAddReferencesToEntity($entity, $contents, NodeInterface::ENTITY_TYPE, $this->contentRepository);
    }

    /**
     * @param mixed $entity
     * @param array $contents
     *
     * @dataProvider provideEntityAndContents
     */
    public function testRemoveReferencesToEntity($entity, array $contents)
    {
        parent::checkRemoveReferencesToEntity($entity, $contents, NodeInterface::ENTITY_TYPE, $this->contentRepository);
    }

    /**
     * @return array
     */
    public function provideEntityAndContents()
    {
        $content = $this->createPhakeContent();
        $contentType = $this->createPhakeContentType();

        $contentId = 'content';
        $content = $this->createPhakeContent($contentId);

        $blockWithoutContent = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($blockWithoutContent)->getAttributes()->thenReturn(array());

        $blockWithContent = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($blockWithContent)->getAttributes()
            ->thenReturn(array(
                'contentType' => null,
                'choiceType' => null,
                'keywords' => null,
                'contentId' => $contentId
            )
        );

        $nodeWithoutContent = $this->createPhakeNode('NodeNoContent');
        Phake::when($nodeWithoutContent)->getBlocks()->thenReturn(array($blockWithoutContent));

        $nodeWithContent = $this->createPhakeNode('NodeWithContent');
        Phake::when($nodeWithContent)->getBlocks()->thenReturn(array($blockWithoutContent, $blockWithContent));

        return array(
            'Content'              => array($content, array()),
            'Content type'         => array($contentType, array()),
            'Node with no content' => array($nodeWithoutContent, array()),
            'Node with content'    => array($nodeWithContent, array($contentId => $content)),
        );
    }
}
