<?php
namespace OpenOrchestra\BackOffice\Tests\BusinessRules\Strategies;

use OpenOrchestra\Backoffice\BusinessRules\Strategies\BusinessActionInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use Phake;
use OpenOrchestra\Backoffice\BusinessRules\Strategies\ContentStrategy;

/**
 * Class ContentStrategyTest
 */
class ContentStrategyTest extends AbstractBaseTestCase
{
    protected $contentRepository;
    protected $siteRepository;
    protected $contextManager;
    protected $strategy;
    protected $allowedContentType = 'allowedContentType';
    protected $notAllowedContentType = 'notAllowedContentType';

    /**
     * setUp
     */
    public function setUp()
    {
        $this->contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');
        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        $this->contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');

        Phake::when($site)->getContentTypes()->thenReturn(array(
            $this->allowedContentType
        ));
        Phake::when($this->contextManager)->getCurrentSiteId()->thenReturn('fakeSiteId');
        Phake::when($this->siteRepository)->findOneBySiteId('fakeSiteId')->thenReturn($site);

        $this->strategy = new ContentStrategy(
            $this->contentRepository,
            $this->siteRepository,
            $this->contextManager
        );
    }

    /**
     * @param int     $content
     * @param boolean $isGranted
     *
     * @dataProvider provideReadContent
     */
    public function testCanRead($content, $isGranted)
    {
        $this->assertSame($isGranted, $this->strategy->canRead($content, array()));
    }

    /**
     * provide group and parameters
     *
     * @return array
     */
    public function provideReadContent()
    {
        $content0 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content0)->getContentType()->thenReturn($this->allowedContentType);

        $content1 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content1)->getContentType()->thenReturn($this->notAllowedContentType);

        return array(
            array($content0, true),
            array($content1, false),
        );
    }

    /**
     * @param ContentInterface $content
     * @param boolean          $isGranted
     *
     * @dataProvider provideEditContent
     */
    public function testCanEdit(ContentInterface $content, $isGranted)
    {
        $this->assertSame($isGranted, $this->strategy->canEdit($content, array()));
    }

    /**
     * provide group and parameters
     *
     * @return array
     */
    public function provideEditContent()
    {
        $status0 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status0)->isBlockedEdition()->thenReturn(false);
        $content0 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content0)->getContentType()->thenReturn($this->allowedContentType);
        Phake::when($content0)->getStatus()->thenReturn($status0);

        $status1 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status1)->isBlockedEdition()->thenReturn(true);
        $content1 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content1)->getContentType()->thenReturn($this->allowedContentType);
        Phake::when($content1)->getStatus()->thenReturn($status1);

        $status2 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status2)->isBlockedEdition()->thenReturn(false);
        $content2 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content2)->getContentType()->thenReturn($this->notAllowedContentType);
        Phake::when($content2)->getStatus()->thenReturn($status2);

        $status3 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status3)->isBlockedEdition()->thenReturn(true);
        $content3 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content3)->getContentType()->thenReturn($this->notAllowedContentType);
        Phake::when($content3)->getStatus()->thenReturn($status3);

        return array(
            array($content0, true),
            array($content1, false),
            array($content2, false),
            array($content3, false),
        );
    }

    /**
     * @param ContentInterface $content
     * @param boolean          $isWithoutAutoUnpublishToState
     * @param boolean          $isGranted
     *
     * @dataProvider provideDeleteContent
     */
    public function testCanDelete(ContentInterface $content, $isWithoutAutoUnpublishToState, $isGranted)
    {
        Phake::when($this->contentRepository)->hasContentIdWithoutAutoUnpublishToState($content->getContentId())->thenReturn($isWithoutAutoUnpublishToState);
        $this->assertSame($isGranted, $this->strategy->canDelete($content, array()));
    }

    /**
     * provide group and parameters
     *
     * @return array
     */
    public function provideDeleteContent()
    {
        $content0 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content0)->getContentType()->thenReturn($this->allowedContentType);
        Phake::when($content0)->getContentId()->thenReturn('fakeContentId0');

        $content1 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content1)->getContentType()->thenReturn($this->notAllowedContentType);
        Phake::when($content1)->getContentId()->thenReturn('fakeContentId1');

        return array(
            array($content0, false, true),
            array($content0, true, false),
            array($content1, false, false),
            array($content1, true, false),
        );
    }

    /**
     * @param ContentInterface $content
     * @param int              $nbrVersions
     * @param boolean          $isGranted
     *
     * @dataProvider provideDeleteVersionContent
     */
    public function testCanDeleteVersion(ContentInterface $content, $nbrVersions, $isGranted)
    {
        Phake::when($this->contentRepository)->countNotDeletedByLanguage(Phake::anyParameters())->thenReturn($nbrVersions);

        $this->assertSame($isGranted, $this->strategy->canDeleteVersion($content, array()));
    }

    /**
     * provide group and parameters
     *
     * @return array
     */
    public function provideDeleteVersionContent()
    {
        $status0 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status0)->isPublishedState()->thenReturn(false);
        $content0 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content0)->getContentType()->thenReturn($this->allowedContentType);
        Phake::when($content0)->getStatus()->thenReturn($status0);

        $status1 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status1)->isPublishedState()->thenReturn(true);
        $content1 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content1)->getContentType()->thenReturn($this->allowedContentType);
        Phake::when($content1)->getStatus()->thenReturn($status1);

        $status2 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status2)->isPublishedState()->thenReturn(false);
        $content2 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content2)->getContentType()->thenReturn($this->notAllowedContentType);
        Phake::when($content2)->getStatus()->thenReturn($status2);

        $status3 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status3)->isPublishedState()->thenReturn(true);
        $content3 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content3)->getContentType()->thenReturn($this->notAllowedContentType);
        Phake::when($content3)->getStatus()->thenReturn($status3);

        return array(
            array($content0, 2, true),
            array($content0, 1, false),
            array($content1, 2, false),
            array($content2, 2, false),
            array($content3, 2, false),
        );
    }

    /**
     * test getActions
     */
    public function testGetActions()
    {
        $this->assertEquals(array(
            BusinessActionInterface::DELETE => 'canDelete',
            ContentStrategy::DELETE_VERSION => 'canDeleteVersion',
            BusinessActionInterface::EDIT => 'canEdit',
            BusinessActionInterface::READ => 'canRead',
        ), $this->strategy->getActions());
    }

    /**
     * test getActions
     */
    public function testType()
    {
        $this->assertEquals(ContentInterface::ENTITY_TYPE, $this->strategy->getType());
    }
}
