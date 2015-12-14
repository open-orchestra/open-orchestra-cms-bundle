<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\Authorization\Voter\RoleHierarchyVoter;
use Phake;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Test RoleHierarchyVoterTest
 */
class RoleHierarchyVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RoleHierarchyVoter
     */
    protected $voter;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $roleHierarchy = Phake::mock('Symfony\Component\Security\Core\Role\RoleHierarchyInterface');

        $this->voter = new RoleHierarchyVoter($roleHierarchy);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter', $this->voter);
    }

    /**
     * Test if user is super admin
     */
    public function testVoteIfSuperAdmin()
    {
        $user = Phake::mock('FOS\UserBundle\Model\UserInterface');
        Phake::when($user)->isSuperAdmin()->thenReturn(true);
        $token = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        Phake::when($token)->getUser()->thenReturn($user);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($token, null, array()));
    }

    /**
     * Test that you call the parent if the user is not superadmin
     *
     * @param bool $hasUser
     *
     * @dataProvider provideHasUser
     */
    public function testVoteIfNoUserOrNotSuperAdmin($hasUser = false)
    {
        $token = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        if ($hasUser) {
            $user = Phake::mock('FOS\UserBundle\Model\UserInterface');
            Phake::when($token)->getUser()->thenReturn($user);
        }
        Phake::when($token)->getRoles()->thenThrow(new TokenGetRoleException());

        $this->setExpectedException('OpenOrchestra\BackofficeBundle\Tests\Security\Authorization\Voter\TokenGetRoleException');
        $this->voter->vote($token, null, array());
    }

    /**
     * @return array
     */
    public function provideHasUser()
    {
        return array(
            array(true),
            array(false),
        );
    }
}

/**
 * Class TokenGetRoleException
 */
class TokenGetRoleException extends \Exception
{
}
