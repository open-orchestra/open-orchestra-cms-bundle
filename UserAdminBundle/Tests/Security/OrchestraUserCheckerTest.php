<?php

namespace OpenOrchestra\UserAdminBundle\Tests\Security;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\UserAdminBundle\Security\OrchestraUserChecker;
use Phake;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class OrchestraUserCheckerTest
 */
class OrchestraUserCheckerTest extends AbstractBaseTestCase
{
    /**
     * @var OrchestraUserChecker
     */
    protected $userChecker;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->userChecker = new OrchestraUserChecker();
    }

    /**
     * test with a not orchestra user
     */
    public function testCheckPreAuthNotOrchestraUserInterface()
    {
        $user = Phake::mock(UserInterface::class);
        Phake::when($user)->isAccountNonLocked()->thenReturn(true);
        Phake::when($user)->isEnabled()->thenReturn(true);
        Phake::when($user)->isAccountNonExpired()->thenReturn(true);

        $this->assertNull($this->userChecker->checkPreAuth($user));
    }

    /**
     * @param UserInterface $user
     * @param boolean       $expectedException
     *
     * @dataProvider provideUser
     */
    public function testCheckPreAuth(UserInterface $user, $expectedException)
    {
        if (true === $expectedException) {
            $this->expectException('OpenOrchestra\UserAdminBundle\Exception\NoRolesException');
        }

        $this->userChecker->checkPreAuth($user);
    }

    /**
     * @return array
     */
    public function provideUser()
    {
        $userNoRoles = $this->createMockUser(array());

        $userRoleDefault = $this->createMockUser(array(\FOS\UserBundle\Model\UserInterface::ROLE_DEFAULT));
        Phake::when($userRoleDefault)->hasRole(\FOS\UserBundle\Model\UserInterface::ROLE_DEFAULT)->thenReturn(true);

        $userRoles= $this->createMockUser(array('fakerole'));

        return array(
            'user no roles'     => array($userNoRoles, true),
            'user role default' => array($userRoleDefault, true),
            'user with roles'   => array($userRoles, false),
        );
    }

    /**
     * @param array $roles
     *
     * @return mixed
     */
    protected function createMockUser(array $roles)
    {
        $user = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($user)->getRoles()->thenReturn($roles);
        Phake::when($user)->isAccountNonLocked()->thenReturn(true);
        Phake::when($user)->isEnabled()->thenReturn(true);
        Phake::when($user)->isAccountNonExpired()->thenReturn(true);

        return $user;
    }
}
