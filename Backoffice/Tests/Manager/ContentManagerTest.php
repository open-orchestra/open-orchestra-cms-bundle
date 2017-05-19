<?php

namespace OpenOrchestra\Backoffice\Tests\Manager;

use OpenOrchestra\Backoffice\Manager\ContentManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
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

    protected $uniqueIdGenerator;
    protected $statusRepository;
    protected $contentAttribute;
    protected $contextManager;
    protected $contentClass;
    protected $contentType;
    protected $keyword;
    protected $content;
    protected $statusInitialLabel = 'statusInitialLabel';
    protected $statusTranslationStateLabel = 'statusTranslationStateLabel';
    protected $statusInitial;
    protected $statusTranslationState;
    protected $statusOutofWorkflow;
    protected $fakeVersion = 'fakeVersion';
    protected $user;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->statusInitial = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($this->statusInitial)->getLabels()->thenReturn(array());
        Phake::when($this->statusInitial)->getName()->thenReturn($this->statusInitialLabel);
        Phake::when($this->statusInitial)->isOutOfWorkflow()->thenReturn(false);

        $this->statusTranslationState = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($this->statusTranslationState)->getLabels()->thenReturn(array());
        Phake::when($this->statusTranslationState)->getName()->thenReturn($this->statusTranslationStateLabel);
        Phake::when($this->statusTranslationState)->isOutOfWorkflow()->thenReturn(false);

        $this->statusOutofWorkflow = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($this->statusOutofWorkflow)->getLabels()->thenReturn(array());
        Phake::when($this->statusOutofWorkflow)->isOutOfWorkflow()->thenReturn(true);

        $this->contentType = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');

        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        Phake::when($this->statusRepository)->findOneByInitial()->thenReturn($this->statusInitial);
        Phake::when($this->statusRepository)->findOneByTranslationState()->thenReturn($this->statusTranslationState);
        Phake::when($this->statusRepository)->findOneByOutOfWorkflow()->thenReturn($this->statusOutofWorkflow);

        $this->keyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        $this->contentAttribute = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentAttributeInterface');

        $this->content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($this->content)->getKeywords()->thenReturn(array($this->keyword));
        Phake::when($this->content)->getAttributes()->thenReturn(array($this->contentAttribute));
        Phake::when($this->content)->getStatus()->thenReturn($this->statusInitial);

        $this->contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface');
        Phake::when($this->contextManager)->getBackOfficeLanguage()->thenReturn('fakeLanguage');

        $this->contentClass = 'OpenOrchestra\ModelBundle\Document\Content';

        $this->uniqueIdGenerator = Phake::mock('OpenOrchestra\Backoffice\Util\UniqueIdGenerator');
        Phake::when($this->uniqueIdGenerator)->generateUniqueId()->thenReturn($this->fakeVersion);

        $tokenStorage = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage');
        $token = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->user = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');

        Phake::when($tokenStorage)->getToken()->thenReturn($token);
        Phake::when($token)->getUser()->thenReturn($this->user);

        $this->manager = new ContentManager(
            $this->statusRepository,
            $this->contextManager,
            $this->contentClass,
            $this->uniqueIdGenerator,
            $tokenStorage
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
     *
     * @dataProvider provideVersionsAndExpected
     */
    public function testNewVersionContent($versionName)
    {
        $newContent = $this->manager->newVersionContent($this->content, $versionName);

        Phake::verify($newContent)->setVersion($this->fakeVersion);
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
            array('fake_version_name'),
            array('foo'),
            array('bar'),
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

        Phake::verify($newContent)->setVersion($this->fakeVersion);
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
     * @param bool $isStatusable
     *
     * @dataProvider provideContentTypeAndLanguage
     */
    public function testInitializeNewContent($contentType, $language, $linkedToSite, $siteId, $isStatusable)
    {
        $userName = 'fakeUserName';
        Phake::when($this->contextManager)->getSiteId()->thenReturn($siteId);
        Phake::when($this->user)->getUsername()->thenReturn($userName);

        $content = $this->manager->initializeNewContent($contentType, $language, $linkedToSite, $isStatusable);

        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\ContentInterface', $content);
        $this->assertSame($language, $content->getLanguage());
        $this->assertSame($contentType, $content->getContentType());
        $this->assertSame($linkedToSite, $content->isLinkedToSite());
        $this->assertSame($siteId, $content->getSiteId());
        $this->assertSame($userName, $content->getCreatedBy());
        if (true === $isStatusable) {
            $this->assertEquals($this->statusInitialLabel, $content->getStatus()->getName());
        } else {
            $this->assertEquals($this->statusOutofWorkflow->isOutOfWorkflow(), true);
        }
    }

    /**
     * @return array
     */
    public function provideContentTypeAndLanguage()
    {
        return array(
            array('news', 'fr', true, '1', true),
            array('car', 'en', true, '2', true),
            array('news', 'fr', false, '3', false),
            array('car', 'en', false, '4', false),
        );
    }
}
