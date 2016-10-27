<?php
namespace OpenOrchestra\Backoffice\Tests\Reference\Strategies;

use Phake;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\Backoffice\Reference\Strategies\ContentInBlockReferenceStrategy;

/**
 * Class ContentInBlockReferenceStrategyTest
 */
class ContentInBlockReferenceStrategyTest extends AbstractReferenceStrategyTest
{
    protected $contentRepository;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');

        $this->strategy = new ContentInBlockReferenceStrategy($this->contentRepository);
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
     * @param string $entityId
     * @param array  $contents
     *
     * @dataProvider provideEntityAndContents
     */
    public function testAddReferencesToEntity($entity, $entityId, array $contents)
    {
        Phake::when($this->contentRepository)->findByContentId(Phake::anyParameters())->thenReturn($contents);

        parent::checkAddReferencesToEntity($entity, $entityId, $contents, BlockInterface::ENTITY_TYPE, $this->contentRepository);
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
        parent::checkRemoveReferencesToEntity($entity, $entityId, $contents, BlockInterface::ENTITY_TYPE, $this->contentRepository);
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
        $blockWithoutContentId = 'fakeBlockWithoutContentId';
        $blockWithContentId = 'fakeBlockWithContentId';

        $blockWithoutContent = Phake::mock('OpenOrchestra\ModelInterface\Model\ReadBlockInterface');
        Phake::when($blockWithoutContent)->getAttributes()->thenReturn(array());
        Phake::when($blockWithoutContent)->getId()->thenReturn($blockWithoutContentId);

        $blockWithContent = Phake::mock('OpenOrchestra\ModelInterface\Model\ReadBlockInterface');
        Phake::when($blockWithContent)->getAttributes()
            ->thenReturn(array(
                'contentType' => null,
                'choiceType' => null,
                'keywords' => null,
                'contentId' => $contentId
            )
        );
        Phake::when($blockWithContent)->getId()->thenReturn($blockWithContentId);

        return array(
            'Content'              => array($content, $contentId, array()),
            'Content type'         => array($contentType, $contentTypeId, array()),
            'Block with no content' => array($blockWithoutContent, $blockWithoutContentId, array()),
            'Block with content'    => array($blockWithContent, $blockWithContentId, array($contentId => $content)),
        );
    }
}
