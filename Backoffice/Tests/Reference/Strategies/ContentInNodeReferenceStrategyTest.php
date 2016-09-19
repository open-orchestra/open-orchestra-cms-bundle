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
     * @param mixed  $entity
     * @param string $entityId
     * @param array  $contents
     *
     * @dataProvider provideEntityAndContents
     */
    public function testAddReferencesToEntity($entity, $entityId, array $contents)
    {
        Phake::when($this->contentRepository)->findByContentId(Phake::anyParameters())->thenReturn($contents);

        parent::checkAddReferencesToEntity($entity, $entityId, $contents, NodeInterface::ENTITY_TYPE, $this->contentRepository);
    }

    /**
     * @param mixed  $entity
     * @param string $entityId
     * @param array  $contents
     *
     * @dataProvider provideEntityAndContents
     */
    public function testRemoveReferencesToEntity($entity, $entityId, array $contents)
    {
        parent::checkRemoveReferencesToEntity($entity, $entityId, $contents, NodeInterface::ENTITY_TYPE, $this->contentRepository);
    }

    /**
     * @return array
     */
    public function provideEntityAndContents()
    {
        $contentId = 'contentId';
        $content = $this->createPhakeContent($contentId);
        $contentTypeId = 'contentTypeId';
        $contentType = $this->createPhakeContentType($contentTypeId);

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

        $nodeWithoutContentId = 'NodeNoContent';
        $nodeWithoutContent = $this->createPhakeNode($nodeWithoutContentId);
        Phake::when($nodeWithoutContent)->getBlocks()->thenReturn(array($blockWithoutContent));

        $nodeWithContentId = 'NodeWithContent';
        $nodeWithContent = $this->createPhakeNode($nodeWithContentId);
        Phake::when($nodeWithContent)->getBlocks()->thenReturn(array($blockWithoutContent, $blockWithContent));

        return array(
            'Content'              => array($content, $contentId, array()),
            'Content type'         => array($contentType, $contentTypeId, array()),
            'Node with no content' => array($nodeWithoutContent, $nodeWithoutContentId, array()),
            'Node with content'    => array($nodeWithContent, $nodeWithContentId, array($contentId => $content)),
        );
    }
}
