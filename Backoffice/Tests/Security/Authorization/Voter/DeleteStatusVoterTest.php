<?php

namespace OpenOrchestra\Backoffice\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\Backoffice\Security\Authorization\Voter\DeleteStatusVoter;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Test DeleteStatusVoterTest
 */
class DeleteStatusVoterTest extends AbstractBaseTestCase
{
    /**
     * @var DeleteStatusVoter
     */
    protected $voter;

    protected $usageFinder;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->usageFinder = Phake::mock('OpenOrchestra\Backoffice\UsageFinder\StatusUsageFinder');

        $this->voter = new DeleteStatusVoter($this->usageFinder);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Security\Core\Authorization\Voter\VoterInterface', $this->voter);
    }

    /**
     * @param bool   $supports
     * @param string $role
     *
     * @dataProvider provideSupportsAndRole
     */
    public function testSupportsAttributes($supports, $role)
    {
        $this->assertSame($supports, $this->voter->supportsAttribute($role));
    }

    /**
     * @return array
     */
    public function provideSupportsAndRole()
    {
        return array(
            array(true, AdministrationPanelStrategy::ROLE_ACCESS_DELETE_STATUS),
            array(false, AdministrationPanelStrategy::ROLE_ACCESS_CREATE_STATUS),
            array(false, AdministrationPanelStrategy::ROLE_ACCESS_STATUS),
            array(false, AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_STATUS),
            array(false, 'foo'),
        );
    }

    /**
     * @param bool   $supports
     * @param string $class
     *
     * @dataProvider provideSupportsAndClass
     */
    public function testSupportsClass($supports, $class)
    {
        $object = Phake::mock($class);

        $this->assertSame($supports, $this->voter->supportsClass($object));
    }

    /**
     * @return array
     */
    public function provideSupportsAndClass()
    {
        return array(
            array(true, 'OpenOrchestra\ModelInterface\Model\StatusInterface'),
            array(false, 'OpenOrchestra\ModelInterface\Model\EmbedStatusInterface'),
            array(false, 'OpenOrchestra\ModelInterface\Model\StatusableInterface'),
            array(false, 'OpenOrchestra\ModelInterface\Model\NodeInterface'),
            array(false, 'OpenOrchestra\ModelInterface\Model\ContentInterface'),
            array(false, 'stdClass'),
        );
    }

    /**
     * @param int  $response
     * @param bool $usageFound
     *
     * @dataProvider provideResponseAndUsageFound
     */
    public function testVote($response, $usageFound)
    {
        $roles = array(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_STATUS);
        $object = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $token = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');

        Phake::when($this->usageFinder)->hasUsage(Phake::anyParameters())->thenReturn($usageFound);

        $this->assertSame($response, $this->voter->vote($token, $object, $roles));
    }

    /**
     * @return array
     */
    public function provideResponseAndUsageFound()
    {
        return array(
            array(VoterInterface::ACCESS_DENIED, true),
            array(VoterInterface::ACCESS_GRANTED, false),
        );
    }
}
