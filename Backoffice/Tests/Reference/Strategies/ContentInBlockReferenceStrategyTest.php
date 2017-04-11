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
    protected $bbcodeParser;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');
        $this->bbcodeParser = Phake::mock('OpenOrchestra\BBcodeBundle\Parser\BBcodeParserInterface');
        $linkTag = Phake::mock('OpenOrchestra\BBcodeBundle\ElementNode\BBcodeElementNodeInterface');
        Phake::when($linkTag)->getAttribute()->thenReturn(array('link'=> '{"label":"link","site_siteId":"2","site_nodeId":"nodeId4", "contentSearch_contentId":"contentId"}'));
        Phake::when($this->bbcodeParser)->parse(Phake::anyParameters())->thenReturn($this->bbcodeParser);
        Phake::when($this->bbcodeParser)->getElementByTagName(Phake::anyParameters())->thenReturn(
            array($linkTag)
        );

        $this->strategy = new ContentInBlockReferenceStrategy($this->contentRepository, $this->bbcodeParser);
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

        $blockWithContentTinymceId = 'blockIdTinymce';
        $blockWithContentTinymce = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        $bbCodeWithLink = 'Some [b]String[b] with [link={"label":"link","site_siteId":"2","site_nodeId":"nodeId4", "contentSearch_contentId":"'.$contentId.'"}]link[/link]';
        Phake::when($blockWithContentTinymce)->getAttributes()->thenReturn(array($bbCodeWithLink));
        Phake::when($blockWithContentTinymce)->getId()->thenReturn($blockWithContentTinymceId);

        return array(
            'Content'                    => array($content, $contentId, array()),
            'Content type'               => array($contentType, $contentTypeId, array()),
            'Block tinymce with content' => array($blockWithContentTinymce, $blockWithContentTinymceId, array($contentId => $content)),
            'Block with no content'      => array($blockWithoutContent, $blockWithoutContentId, array()),
            'Block with content'         => array($blockWithContent, $blockWithContentId, array($contentId => $content)),
        );
    }
}
