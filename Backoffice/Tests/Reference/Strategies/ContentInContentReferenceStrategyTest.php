<?php

namespace OpenOrchestra\Backoffice\Tests\Reference\Strategies;

use OpenOrchestra\Backoffice\Reference\Strategies\ContentInContentReferenceStrategy;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use Phake;

/**
 * Class ContentInContentReferenceStrategyTest
 */
class ContentInContentReferenceStrategyTest extends AbstractReferenceStrategyTest
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

        $this->strategy = new ContentInContentReferenceStrategy($this->contentRepository, $this->bbcodeParser);
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
            'Content'      => array($content, true),
            'Block'         => array($block, false),
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

        parent::checkAddReferencesToEntity($entity, $entityId, $contents, ContentInterface::ENTITY_TYPE, $this->contentRepository);
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
        parent::checkRemoveReferencesToEntity($entity, $entityId, $contents, ContentInterface::ENTITY_TYPE, $this->contentRepository);
    }

    /**
     * @return array
     */
    public function provideEntityAndContents()
    {
        $contentId = 'contentEmbeddedId';
        $content = $this->createPhakeContent($contentId);

        $contentEmbeddedId = 'contentEmbeddedId';
        $contentEmbedded = $this->createPhakeContent($contentEmbeddedId);

        $attributeContent = $this->createPhakeContentAttribute(
            array('contentId' => $contentEmbeddedId),
            'embedded_content'
        );

        $bbCodeWithLink = 'Some [b]String[b] with [link={"label":"link","site_siteId":"2","site_nodeId":"nodeId4", "contentSearch_contentId":"'.$contentId.'"}]link[/link]';
        $attributeContentTinymce = $this->createPhakeContentAttribute(
            $bbCodeWithLink,
            'tinymce'
        );

        $contentWithContent = $this->createPhakeContent($contentId, array($attributeContent));
        $contentTinymceContent = $this->createPhakeContent($contentId, array($attributeContentTinymce));

        return array(
            'Content with no content'    => array($content, $contentId, array()),
            'Content with  content'      => array($contentWithContent, $contentEmbeddedId, array($contentEmbeddedId => $contentEmbedded)),
            'Content tinymce content'    => array($contentTinymceContent, $contentId, array($contentId => $content)),
        );
    }
}
