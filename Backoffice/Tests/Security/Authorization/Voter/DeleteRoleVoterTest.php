<?php

namespace OpenOrchestra\Backoffice\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\Backoffice\Security\Authorization\Voter\DeleteRoleVoter;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Test DeleteRoleVoterTest
 */
class DeleteRoleVoterTest extends AbstractBaseTestCase
{
    /**
     * @var DeleteRoleVoter
     */
    protected $voter;

    protected $usageFinder;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->usageFinder = Phake::mock('OpenOrchestra\Backoffice\UsageFinder\RoleUsageFinder');

        $this->voter = new DeleteRoleVoter($this->usageFinder);
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
            array(true, AdministrationPanelStrategy::ROLE_ACCESS_DELETE_ROLE),
            array(false, AdministrationPanelStrategy::ROLE_ACCESS_CREATE_ROLE),
            array(false, AdministrationPanelStrategy::ROLE_ACCESS_ROLE),
            array(false, AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_ROLE),
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
            array(true, 'OpenOrchestra\ModelInterface\Model\RoleInterface'),
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
        $roles = array(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_ROLE);
        $object = Phake::mock('OpenOrchestra\ModelInterface\Model\RoleInterface');
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
