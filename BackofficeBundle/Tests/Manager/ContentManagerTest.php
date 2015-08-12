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

    protected $contentTypeRepository;
    protected $contentRepository;
    protected $contentAttribute;
    protected $contextManager;
    protected $contentClass;
    protected $contentType;
    protected $keyword;
    protected $content;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contentType = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        $this->contentTypeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface');
        Phake::when($this->contentTypeRepository)->findOneByContentTypeIdInLastVersion(Phake::anyParameters())->thenReturn($this->contentType);

        $this->keyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        $this->contentAttribute = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentAttributeInterface');

        $this->content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($this->content)->getKeywords()->thenReturn(array($this->keyword));
        Phake::when($this->content)->getAttributes()->thenReturn(array($this->contentAttribute));

        $this->contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');
        Phake::when($this->contextManager)->getCurrentLocale()->thenReturn('fakeLanguage');

        $this->contentClass = 'OpenOrchestra\ModelBundle\Document\Content';

        $this->manager = new ContentManager($this->contextManager, $this->contentClass, $this->contentTypeRepository);
    }

    /**
     * test new language creation
     */
    public function testCreateNewLanguageContent()
    {
        $language = 'fr';
        $newContent = $this->manager->createNewLanguageContent($this->content, $language);

        Phake::verify($newContent, Phake::times(1))->setVersion(1);
        Phake::verify($newContent)->setStatus(null);
        Phake::verify($newContent)->setLanguage($language);
    }

    /**
     * @param int $version
     * @param int $expectedVersion
     *
     * @dataProvider provideVersionsAndExpected
     */
    public function testDuplicateNode($version, $lastVersion, $expectedVersion)
    {
        Phake::when($this->content)->getVersion()->thenReturn($version);
        $lastContent = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($lastContent)->getVersion()->thenReturn($lastVersion);
        $newContent = $this->manager->duplicateContent($this->content, $lastContent);

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
            array(1, 1, 2),
            array(5, 6, 7),
        );
    }

    /**
     * @param string $contentType
     * @param string $language
     * @param bool   $linkedToSite
     * @param string $siteId
     *
     * @dataProvider provideContentTypeAndLanguage
     */
    public function testInitializeNewContent($contentType, $language, $linkedToSite, $siteId)
    {
        Phake::when($this->contentType)->isLinkedToSite()->thenReturn($linkedToSite);
        Phake::when($this->contextManager)->getDefaultLocale()->thenReturn($language);
        Phake::when($this->contextManager)->getCurrentSiteId()->thenReturn($siteId);

        $content = $this->manager->initializeNewContent($contentType, $language);

        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\ContentInterface', $content);
        $this->assertSame($language, $content->getLanguage());
        $this->assertSame($contentType, $content->getContentType());
        $this->assertSame($linkedToSite, $content->isLinkedToSite());
        $this->assertSame($siteId, $content->getSiteId());
    }

    /**
     * @return array
     */
    public function provideContentTypeAndLanguage()
    {
        return array(
            array('news', 'fr', true, '1'),
            array('car', 'en', true, '2'),
            array('news', 'fr', false, '3'),
            array('car', 'en', false, '4'),
        );
    }
}
