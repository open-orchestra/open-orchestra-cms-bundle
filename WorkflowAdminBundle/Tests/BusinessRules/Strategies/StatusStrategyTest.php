<?php
namespace OpenOrchestra\GroupBundle\Tests\BusinessRules\Strategies;

use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\WorkflowAdminBundle\BusinessRules\Strategies\StatusStrategy;

/**
 * Class StatusStrategyTest
 */
class StatusStrategyTest extends AbstractBaseTestCase
{
    protected $statusUsageFinder;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->statusUsageFinder = Phake::mock('OpenOrchestra\Backoffice\UsageFinder\StatusUsageFinder');
        $this->strategy = new StatusStrategy($this->statusUsageFinder);
    }

    /**
     * @param StatusInterface $status
     * @param boolean         $hasUsage
     * @param boolean         $isGranted
     *
     * @dataProvider provideStatusAndUsage
     */
    public function testCanDelete(StatusInterface $status, $hasUsage, $isGranted)
    {
        Phake::when($this->statusUsageFinder)->hasUsage(Phake::anyParameters())->thenReturn($hasUsage);
        $this->assertSame($isGranted, $this->strategy->canDelete($status, array()));
    }

    /**
     * provide status and hasUsage
     *
     * @return array
     */
    public function provideStatusAndUsage()
    {
        $status0 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status0)->isInitialState()->thenReturn(true);
        Phake::when($status0)->isPublishedState()->thenReturn(false);
        Phake::when($status0)->isTranslationState()->thenReturn(false);
        Phake::when($status0)->isAutoPublishFromState()->thenReturn(false);
        Phake::when($status0)->isAutoUnpublishToState()->thenReturn(false);

        $status1 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status1)->isInitialState()->thenReturn(false);
        Phake::when($status1)->isPublishedState()->thenReturn(true);
        Phake::when($status1)->isTranslationState()->thenReturn(false);
        Phake::when($status1)->isAutoPublishFromState()->thenReturn(false);
        Phake::when($status1)->isAutoUnpublishToState()->thenReturn(false);

        $status2 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status2)->isInitialState()->thenReturn(false);
        Phake::when($status2)->isPublishedState()->thenReturn(false);
        Phake::when($status2)->isTranslationState()->thenReturn(true);
        Phake::when($status2)->isAutoPublishFromState()->thenReturn(false);
        Phake::when($status2)->isAutoUnpublishToState()->thenReturn(false);

        $status3 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status3)->isInitialState()->thenReturn(false);
        Phake::when($status3)->isPublishedState()->thenReturn(false);
        Phake::when($status3)->isTranslationState()->thenReturn(false);
        Phake::when($status3)->isAutoPublishFromState()->thenReturn(true);
        Phake::when($status3)->isAutoUnpublishToState()->thenReturn(false);

        $status4 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status4)->isInitialState()->thenReturn(false);
        Phake::when($status4)->isPublishedState()->thenReturn(false);
        Phake::when($status4)->isTranslationState()->thenReturn(false);
        Phake::when($status4)->isAutoPublishFromState()->thenReturn(false);
        Phake::when($status4)->isAutoUnpublishToState()->thenReturn(true);

        $status5 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status5)->isInitialState()->thenReturn(false);
        Phake::when($status5)->isPublishedState()->thenReturn(false);
        Phake::when($status5)->isTranslationState()->thenReturn(false);
        Phake::when($status5)->isAutoPublishFromState()->thenReturn(false);
        Phake::when($status5)->isAutoUnpublishToState()->thenReturn(false);

        return array(
            array($status0, false, false),
            array($status1, false, false),
            array($status2, false, false),
            array($status3, false, false),
            array($status4, false, false),
            array($status5, false, true),
            array($status5, true, false),
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
        $this->assertEquals(StatusInterface::ENTITY_TYPE, $this->strategy->getType());
    }
}
