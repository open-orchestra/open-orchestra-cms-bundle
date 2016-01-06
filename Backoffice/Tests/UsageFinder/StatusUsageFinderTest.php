<?php

namespace OpenOrchestra\Backoffice\Tests\UsageFinder;

use OpenOrchestra\Backoffice\UsageFinder\StatusUsageFinder;
use Phake;

/**
 * Test StatusUsageFinderTest
 */
class StatusUsageFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StatusUsageFinder
     */
    protected $finder;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->finder = new StatusUsageFinder();
    }

    /**
     * @param bool $hasUsage
     * @param bool $elementPresent1
     * @param bool $elementPresent2
     *
     * @dataProvider provideUsageAndRepositoryResponse
     */
    public function testHasUsage($hasUsage, $elementPresent1, $elementPresent2)
    {
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');

        $repository1 = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusableElementRepositoryInterface');
        Phake::when($repository1)->hasStatusedElement(Phake::anyParameters())->thenReturn($elementPresent1);
        $repository2 = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusableElementRepositoryInterface');
        Phake::when($repository2)->hasStatusedElement(Phake::anyParameters())->thenReturn($elementPresent2);

        $this->finder->addRepository($repository1);
        $this->finder->addRepository($repository2);

        $this->assertSame($hasUsage, $this->finder->hasUsage($status));
    }

    /**
     * @return array
     */
    public function provideUsageAndRepositoryResponse()
    {
        return array(
            array(true, true, true),
            array(true, true, false),
            array(true, false, true),
            array(false, false, false),
        );
    }
}
