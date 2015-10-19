<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Collector;

use OpenOrchestra\Backoffice\Collector\RoleCollector;

/**
 * Class RoleCollectorTest
 */
class RoleCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RoleCollector
     */
    protected $collector;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->collector = new RoleCollector();
    }

    /**
     * @param array $newRoles
     * @param array $expectedRoles
     *
     * @dataProvider provideRolesAndExpected
     */
    public function testAddAndGetRoles(array $newRoles, array $expectedRoles)
    {
        foreach ($newRoles as $newRole) {
            $this->collector->addRole($newRole);
        }

        $this->assertSame($expectedRoles, $this->collector->getRoles());
    }

    /**
     * @return array
     */
    public function provideRolesAndExpected()
    {
        return array(
            array(array(), array()),
            array(array('foo'), array('foo')),
            array(array('foo', 'foo'), array('foo')),
            array(array('foo', 'bar'), array('foo', 'bar')),
        );
    }
}
