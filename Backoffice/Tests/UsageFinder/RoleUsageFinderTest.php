<?php

namespace OpenOrchestra\Backoffice\Tests\UsageFinder;

use OpenOrchestra\Backoffice\UsageFinder\RoleUsageFinder;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Test RoleUsageFinderTest
 */
class RoleUsageFinderTest extends AbstractBaseTestCase
{
    /**
     * @var RoleUsageFinder
     */
    protected $finder;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->finder = new RoleUsageFinder();
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
        $role = Phake::mock('OpenOrchestra\ModelInterface\Model\RoleInterface');

        $repository1 = Phake::mock('OpenOrchestra\ModelInterface\Repository\RoleableElementRepositoryInterface');
        Phake::when($repository1)->hasElementWithRole(Phake::anyParameters())->thenReturn($elementPresent1);
        $repository2 = Phake::mock('OpenOrchestra\ModelInterface\Repository\RoleableElementRepositoryInterface');
        Phake::when($repository2)->hasElementWithRole(Phake::anyParameters())->thenReturn($elementPresent2);

        $this->finder->addRepository($repository1);
        $this->finder->addRepository($repository2);

        $this->assertSame($hasUsage, $this->finder->hasUsage($role));
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
