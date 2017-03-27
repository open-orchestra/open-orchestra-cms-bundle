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

    /**
     * setUp
     */
    public function setUp()
    {
        $this->contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');

        $this->strategy = new ContentTypeStrategy(
            $this->contentRepository);
    }

    /**
     * @param int $countByContentType
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
     * test getActions
     */
    public function testGetActions()
    {
        $this->assertEquals(array(
            ContributionActionInterface::DELETE => 'canDelete',
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
