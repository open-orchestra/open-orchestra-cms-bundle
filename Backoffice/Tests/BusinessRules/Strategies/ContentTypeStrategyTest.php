<?php
namespace OpenOrchestra\BackOffice\Tests\BusinessRules\Strategies;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use Phake;
use OpenOrchestra\Backoffice\BusinessRules\Strategies\ContentTypeStrategy;

/**
 * Class ContentTypeStrategyTest
 */
class ContentTypeStrategyTest extends AbstractBaseTestCase
{
    protected $contentRepository;
    protected $strategy;
    protected $siteRepository;
    protected $contextManager;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');
        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        $this->contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');

        $this->strategy = new ContentTypeStrategy(
            $this->contentRepository,
            $this->contextManager,
            $this->siteRepository
        );
    }

    /**
     * @param int  $countByContentType
     * @param bool $isGranted
     *
     * @dataProvider provideDeleteContentType
     */
    public function testCanDelete($countByContentType, $isGranted)
    {
        Phake::when($this->contentRepository)->countByContentType(Phake::anyParameters())->thenReturn($countByContentType);
        $this->assertSame($isGranted, $this->strategy->canDelete(Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface'), array()));
    }

    /**
     * provide group and parameters
     *
     * @return array
     */
    public function provideDeleteContentType()
    {
        return array(
            array(0, true),
            array(1, false),
        );
    }

    /**
     * @param string $contentTypeId
     * @param array  $availableContentType
     * @param bool   $isGranted
     *
     * @dataProvider provideCanReadListContentType
     */
    public function testCanReadList($contentTypeId, array $availableContentType, $isGranted)
    {
        $contentType = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($contentType )->getContentTypeId()->thenReturn($contentTypeId);

        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site)->getContentTypes()->thenReturn($availableContentType);
        Phake::when($this->siteRepository)->findOneBySiteId(Phake::anyParameters())->thenReturn($site);
        $this->assertSame($isGranted, $this->strategy->canReadList($contentType, array()));
    }

    /**
     * @return array
     */
    public function provideCanReadListContentType()
    {
        return array(
            array('test', array(), false),
            array('test', array('other'), false),
            array('test', array('other', 'test'), true),
        );
    }

    /**
     * test getActions
     */
    public function testGetActions()
    {
        $this->assertEquals(array(
            ContributionActionInterface::DELETE => 'canDelete',
            ContentTypeStrategy::READ_LIST => 'canReadList',
        ), $this->strategy->getActions());
    }

    /**
     * test getActions
     */
    public function testType()
    {
        $this->assertEquals(ContentTypeInterface::ENTITY_TYPE, $this->strategy->getType());
    }
}
