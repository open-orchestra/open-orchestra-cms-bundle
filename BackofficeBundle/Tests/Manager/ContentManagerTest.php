<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Manager;

use OpenOrchestra\BackofficeBundle\Manager\ContentManager;
use Phake;

/**
 * Class ContentManagerTest
 */
class ContentManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentManager
     */
    protected $manager;

    protected $contentRepository;
    protected $contentAttribute;
    protected $contextManager;
    protected $contentClass;
    protected $keyword;
    protected $content;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->keyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        $this->contentAttribute = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentAttributeInterface');

        $this->content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($this->content)->getKeywords()->thenReturn(array($this->keyword));
        Phake::when($this->content)->getAttributes()->thenReturn(array($this->contentAttribute));

        $this->contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');
        Phake::when($this->contextManager)->getCurrentLocale()->thenReturn('fakeLanguage');

        $this->contentClass = 'OpenOrchestra\ModelBundle\Document\Content';

        $this->manager = new ContentManager($this->contextManager, $this->contentClass);
    }

    /**
     * test new langage creation
     */
    public function testCreateNewLanguageContent()
    {
        $language = 'fr';
        $newContent = $this->manager->createNewLanguageContent($this->content, $language);

        Phake::verify($newContent, Phake::times(2))->setVersion(1);
        Phake::verify($newContent)->setStatus(null);
        Phake::verify($newContent)->setLanguage($language);
    }

    /**
     * @param int $version
     * @param int $expectedVersion
     *
     * @dataProvider provideVersionsAndExpected
     */
    public function testDuplicateNode($version, $expectedVersion)
    {
        Phake::when($this->content)->getVersion()->thenReturn($version);

        $newContent = $this->manager->duplicateContent($this->content);

        Phake::verify($newContent)->setVersion($expectedVersion);
        Phake::verify($newContent)->setStatus(null);
        Phake::verify($newContent)->addKeyword($this->keyword);
        Phake::verify($newContent)->addAttribute($this->contentAttribute);
    }

    /**
     * @return array
     */
    public function provideVersionsAndExpected()
    {
        return array(
            array(1, 2),
            array(5, 6),
        );
    }

    /**
     * @param string $contentType
     * @param string $language
     *
     * @dataProvider provideContentTypeAndLanguage
     */
    public function testInitializeNewContent($contentType, $language)
    {
        Phake::when($this->contextManager)->getDefaultLocale()->thenReturn($language);

        $content = $this->manager->initializeNewContent($contentType, $language);

        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\ContentInterface', $content);
        $this->assertSame($language, $content->getLanguage());
        $this->assertSame($contentType, $content->getContentType());
    }

    /**
     * @return array
     */
    public function provideContentTypeAndLanguage()
    {
        return array(
            array('news', 'fr'),
            array('car', 'en'),
        );
    }
}
