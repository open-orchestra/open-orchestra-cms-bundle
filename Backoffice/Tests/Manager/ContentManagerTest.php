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

        $this->contentTypeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface');
        Phake::when($this->contentTypeRepository)->findOneByContentTypeIdInLastVersion(Phake::anyParameters())->thenReturn($this->contentType);

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

        $this->manager = new ContentManager($this->contentTypeRepository, $this->statusRepository, $this->contextManager, $this->versionableSaver, $this->contentClass);
    }

    /**
     * test new language creation
     */
    public function testCreateNewLanguageContent()
    {
        $language = 'fr';
        $newContent = $this->manager->createNewLanguageContent($this->content, $language);

        Phake::verify($newContent, Phake::times(1))->setVersion(1);
        Phake::verify($newContent)->setLanguage($language);
        Phake::verify($newContent)->setStatus($this->statusTranslationState);
    }

    /**
     * @param int  $version
     * @param int  $version
     * @param bool $getVersion
     *
     * @dataProvider provideVersionsAndExpected
     */
    public function testNewVersionContent($version, $getVersion, $lastVersion, $expectedVersion)
    {
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');

        $contentType = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($contentType)->isDefiningVersionable()->thenReturn($getVersion);

        $contentTypeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface');
        Phake::when($contentTypeRepository)->findOneByContentTypeIdInLastVersion(Phake::anyParameters())->thenReturn($contentType);

        $manager = new ContentManager($contentTypeRepository, $this->statusRepository, $this->contextManager, $this->versionableSaver, $this->contentClass);

        Phake::when($content)->getVersion()->thenReturn($version);
        $lastContent = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($lastContent)->getVersion()->thenReturn($lastVersion);

        $newContent = $manager->newVersionContent($this->content, $lastContent);

        Phake::verify($newContent)->setVersion($expectedVersion);
        Phake::verify($newContent)->setStatus(null);
        Phake::verify($newContent)->addKeyword($this->keyword);
        Phake::verify($newContent)->setCurrentlyPublished(false);
        Phake::verify($newContent)->addAttribute($this->contentAttribute);
    }

    /**
     * @return array
     */
    public function provideVersionsAndExpected()
    {
        return array(
            array(1, true, 1, 2),
            array(5, true, 6, 7),
            array(1, false, 1, 1),
            array(5, false, 6, 1),
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
        Phake::verify($newContent)->setStatus(null);
        Phake::verify($newContent)->addKeyword($this->keyword);
        Phake::verify($newContent)->setCurrentlyPublished(false);
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
