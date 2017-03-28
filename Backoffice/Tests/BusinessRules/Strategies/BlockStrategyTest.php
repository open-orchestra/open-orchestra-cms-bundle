<?php
namespace OpenOrchestra\BackOffice\Tests\BusinessRules\Strategies;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Phake;
use OpenOrchestra\Backoffice\BusinessRules\Strategies\BlockStrategy;

/**
 * Class BlockStrategyTest
 */
class BlockStrategyTest extends AbstractBaseTestCase
{
    protected $nodeRepository;
    protected $strategy;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');

        $this->strategy = new BlockStrategy($this->nodeRepository);
    }

    /**
     * @param int     $count
     * @param boolean $isTransverse
     * @param boolean $isGranted
     *
     * @dataProvider provideBlockAndParameters
     */
    public function testCanDelete($count, $isTransverse, $isGranted)
    {
        $id = 'fakeId';

        $block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($block)->getId()->thenReturn($id);
        Phake::when($block)->isTransverse()->thenReturn($isTransverse);

        Phake::when($this->nodeRepository)->countBlockUsed($id)->thenReturn($count);

        $this->assertSame($isGranted, $this->strategy->canDelete($block, array()));
    }

    /**
     * provide group and parameters
     *
     * @return array
     */
    public function provideBlockAndParameters()
    {
        return array(
            array(0, true, true),
            array(1, true, false),
            array(0, false, true),
            array(1, false, true),
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
        $this->assertEquals(BlockInterface::ENTITY_TYPE, $this->strategy->getType());
    }
}
