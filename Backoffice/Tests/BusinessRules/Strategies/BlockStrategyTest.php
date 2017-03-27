<?php
namespace OpenOrchestra\BackOffice\Tests\BusinessRules\Strategies;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
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
     * @param int            $count
     * @param array          $parameters
     * @param boolean        $isGranted
     *
     * @dataProvider provideBlockAndParameters
     */
    public function testCanDelete($count, array $parameters, $isGranted)
    {
        $id = 'fakeId';

        $block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($block)->getId()->thenReturn($id);

        Phake::when($this->nodeRepository)->countBlockUsed($id)->thenReturn($count);

        $this->assertSame($isGranted, $this->strategy->canDelete($block, $parameters));
    }

    /**
     * provide group and parameters
     *
     * @return array
     */
    public function provideBlockAndParameters()
    {
        return array(
            array(0, array(), true),
            array(0, array('isTransverse' => true), true),
            array(0, array('isTransverse' => false), false),
            array(1, array(), false),
            array(1, array('isTransverse' => true), false),
            array(1, array('isTransverse' => false), false),
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
}
