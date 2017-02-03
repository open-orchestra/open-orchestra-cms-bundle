<?php

namespace OpenOrchestra\Backoffice\Tests\Manager;

use OpenOrchestra\Backoffice\Manager\ContentManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Saver\VersionableSaverInterface;
use Phake;

/**
 * Class ContentManagerTest
 */
class ContentManagerTest extends AbstractBaseTestCase
{
    /**
     * @var ContentManager
     */
    protected $manager;

    /** @var  VersionableSaverInterface */
    protected $versionableSaver;
    protected $contentTypeRepository;
    protected $statusRepository;
    protected $contentRepository;
    protected $contentAttribute;
    protected $contextManager;
    protected $contentClass;
    protected $contentType;
    protected $keyword;
    protected $content;
    protected $statusInitialLabel = 'statusInitialLabel';
    protected $statusTranslationStateLabel = 'statusTranslationStateLabel';
    protected $statusInitial = 'statusTranslationStateLabel';
    protected $statusTranslationState = 'statusTranslationStateLabel';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->statusInitial = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($this->statusInitial)->getLabels()->thenReturn(array());
        Phake::when($this->statusInitial)->getName()->thenReturn($this->statusInitialLabel);
        $this->statusTranslationState = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($this->statusTranslationState)->getLabels()->thenReturn(array());
        Phake::when($this->statusTranslationState)->getName()->thenReturn($this->statusTranslationStateLabel);

        $this->contentType = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');

        $this->contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');

        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        Phake::when($this->statusRepository)->findOneByInitial()->thenReturn($this->statusInitial);
        Phake::when($this->statusRepository)->findOneByTranslationState()->thenReturn($this->statusTranslationState);

        $this->keyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        $this->contentAttribute = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentAttributeInterface');

        $this->content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($this->content)->getKeywords()->thenReturn(array($this->keyword));
        Phake::when($this->content)->getAttributes()->thenReturn(array($this->contentAttribute));

        $this->contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');
        Phake::when($this->contextManager)->getCurrentLocale()->thenReturn('fakeLanguage');

        $this->contentClass = 'OpenOrchestra\ModelBundle\Document\Content';

        $this->versionableSaver = Phake::mock('OpenOrchestra\ModelBundle\Saver\VersionableSaver');

        $this->manager = new ContentManager(
            $this->contentRepository,
            $this->statusRepository,
            $this->contextManager,
            $this->versionableSaver,
            $this->contentClass
        );
    }

    /**
     * test new language creation
     */
    public function testCreateNewLanguageContent()
    {
        $language = 'fr';
        $newContent = $this->manager->createNewLanguageContent($this->content, $language);

        Phake::verify($newContent)->setLanguage($language);
        Phake::verify($newContent)->setStatus($this->statusTranslationState);
    }

    /**
     * @param int  $versionName
     * @param int  $lastVersion
     * @param bool $expectedVersion
     *
     * @dataProvider provideVersionsAndExpected
     */
    public function testNewVersionContent($versionName, $lastVersion, $expectedVersion)
    {
        $lastContent = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($lastContent)->getVersion()->thenReturn($lastVersion);

        Phake::when($this->contentRepository)->findOneByLanguage(Phake::anyParameters())->thenReturn($lastContent);

        $newContent = $this->manager->newVersionContent($this->content, $versionName);

        Phake::verify($newContent)->setVersion($expectedVersion);
        Phake::verify($newContent)->setStatus($this->statusInitial);
        Phake::verify($newContent)->addKeyword($this->keyword);
        Phake::verify($newContent)->addAttribute($this->contentAttribute);
    }

    /**
     * @return array
     */
    public function provideVersionsAndExpected()
    {
        return array(
            array('fake_version_name', 1, 2),
            array('foo', 6, 7),
            array('bar', 5, 6),
        );
    }

    /**
     * @param string $contentId
     *
     * @dataProvider provideContentId
     */
    public function testDuplicateContent($contentId)
    {
        $newContent = $this->manager->duplicateContent($this->content, $contentId);

        Phake::verify($newContent)->setVersion(1);
        Phake::verify($newContent)->setStatus($this->statusInitial);
        Phake::verify($newContent)->addKeyword($this->keyword);
        Phake::verify($newContent)->addAttribute($this->contentAttribute);
        Phake::verify($newContent)->setContentId($contentId);
    }

    /**
     * @return array
     */
    public function provideContentId()
    {
        return array(
            array(null),
            array('fakeContentId'),
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
        Phake::when($this->contextManager)->getCurrentSiteId()->thenReturn($siteId);

        $content = $this->manager->initializeNewContent($contentType, $language, $linkedToSite);

        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\ContentInterface', $content);
        $this->assertSame($language, $content->getLanguage());
        $this->assertSame($contentType, $content->getContentType());
        $this->assertSame($linkedToSite, $content->isLinkedToSite());
        $this->assertSame($siteId, $content->getSiteId());
        $this->assertEquals($this->statusInitialLabel, $content->getStatus()->getName());
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
