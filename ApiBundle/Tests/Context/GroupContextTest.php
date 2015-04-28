<?php

namespace OpenOrchestra\ApiBundle\Tests\Context;

use OpenOrchestra\ApiBundle\Context\GroupContext;
use Phake;

/**
 * Test GroupContextTest
 */
class GroupContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GroupContext
     */
    protected $context;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->context = new GroupContext();
    }

    /**
     * @param string $groupName
     *
     * @dataProvider provideGroupName
     */
    public function testContainsGroup($groupName)
    {
        $this->context->setGroups(array($groupName));

        $this->assertTrue($this->context->hasGroup($groupName));
        $this->assertFalse($this->context->hasGroup("foo"));

    }

    /**
     * @return array
     */
    public function provideGroupName()
    {
        return array(
            array('GROUP_1'),
            array('FOO_GROUP'),
        );
    }
}
