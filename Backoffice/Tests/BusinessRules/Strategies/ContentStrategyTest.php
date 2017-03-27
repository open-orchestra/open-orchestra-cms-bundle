<?php
namespace OpenOrchestra\BackOffice\Tests\BusinessRules\Strategies;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\BusinessRules\Strategies\ContentStrategy;

/**
 * Class ContentStrategyTest
 */
class ContentStrategyTest extends AbstractBaseTestCase
{
    protected $contentRepository;
    protected $siteRepository;
    protected $contextManeger;
    protected $strategy;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');
        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        $this->contextManeger = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');

        Phake::when($site)->getContentTypes()->thenReturn(array(
            'allowedContentType'
        ));
        Phake::when($this->contextManeger)->getCurrentSiteId()->thenReturn('fakeSiteId');
        Phake::when($this->siteRepository)->findOneBySiteId('fakeSiteId')->thenReturn($site);

        $this->strategy = new ContentStrategy(
            $this->contentRepository,
            $this->siteRepository,
            $this->contextManeger);
    }

    /**
     * @param int            $content
     * @param boolean        $isGranted
     *
     * @dataProvider provideContent
     */
    public function testCanRead($content, $isGranted)
    {
        $this->assertSame($isGranted, $this->strategy->canRead($content, array()));
    }

    /**
     * @param int            $content
     * @param boolean        $isGranted
     *
     * @dataProvider provideContent
     */
    public function testCanEdit($content, $isGranted)
    {
        $this->assertSame($isGranted, $this->strategy->canRead($content, array()));
    }


    /**
     * provide group and parameters
     *
     * @return array
     */
    public function provideContent()
    {

        $content0 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content0)->getContentType()->thenReturn('allowedContentType');

        $content1 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content1)->getContentType()->thenReturn('notAllowedContentType');

        return array(
            array($content0, true),
            array($content0, false),
        );
    }

    /**
     * test getActions
     */
    public function testGetActions()
    {
        $this->assertEquals(array(
            ContributionActionInterface::DELETE => 'canDelete',
            ContentStrategy::DELETE_VERSION => 'canDeleteVersion',
            ContributionActionInterface::EDIT => 'canEdit',
            ContributionActionInterface::READ => 'canRead',
        ), $this->strategy->getActions());
    }
}
