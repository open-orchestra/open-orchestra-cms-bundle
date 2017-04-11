<?php
namespace OpenOrchestra\BackOffice\Tests\BusinessRules\Strategies;

use OpenOrchestra\Backoffice\BusinessRules\Strategies\BusinessActionInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use Phake;
use OpenOrchestra\Backoffice\BusinessRules\Strategies\KeywordStrategy;

/**
 * Class KeywordStrategyTest
 */
class KeywordStrategyTest extends AbstractBaseTestCase
{
    protected $strategy;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->strategy = new KeywordStrategy();
    }

    /**
     * @param bool $countByKeyword
     *
     * @dataProvider provideDeleteKeyword
     */
    public function testCanDelete($isUsed, $isGranted)
    {
        $keyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        Phake::when($keyword)->isUsed()->thenReturn($isUsed);
        $this->assertSame($isGranted, $this->strategy->canDelete($keyword, array()));
    }

    /**
     * provide group and parameters
     *
     * @return array
     */
    public function provideDeleteKeyword()
    {
        return array(
            array(false, true),
            array(true, false),
        );
    }

    /**
     * test getActions
     */
    public function testGetActions()
    {
        $this->assertEquals(array(
            BusinessActionInterface::DELETE => 'canDelete',
        ), $this->strategy->getActions());
    }

    /**
     * test getActions
     */
    public function testType()
    {
        $this->assertEquals(KeywordInterface::ENTITY_TYPE, $this->strategy->getType());
    }
}
