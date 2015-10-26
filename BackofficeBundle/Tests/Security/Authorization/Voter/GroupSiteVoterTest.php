<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Security\Authorization\Voter;

use OpenOrchestra\BackofficeBundle\Security\Authorization\Voter\GroupSiteVoter;
use Phake;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Test GroupSiteVoterTest
 */
class GroupSiteVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GroupSiteVoter
     */
    protected $voter;

    protected $contextManager;
    protected $siteRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contextManager = Phake::mock('OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface');
        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');

        $this->voter = new GroupSiteVoter($this->contextManager, $this->siteRepository);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Security\Core\Authorization\Voter\VoterInterface', $this->voter);
    }

    /**
     * @param string $class
     *
     * @dataProvider provideClassName
     */
    public function testSupportsClass($class)
    {
        $this->assertTrue($this->voter->supportsClass($class));
    }

    /**
     * @return array
     */
    public function provideClassName()
    {
        return array(
            array('StdClass'),
            array('class'),
            array('string'),
            array('Symfony\Component\Security\Core\Authorization\Voter\VoterInterface'),
            array('OpenOrchestra\BackofficeBundle\Model\GroupInterface'),
        );
    }

    /**
     * @param string $attribute
     * @param bool   $supports
     *
     * @dataProvider provideAttributeAndSupport
     */
    public function testSupportsAttribute($attribute, $supports)
    {
        $this->assertSame($supports, $this->voter->supportsAttribute($attribute));
    }

    /**
     * @return array
     */
    public function provideAttributeAndSupport()
    {
        return array(
            array('ROLE_ACCESS_GENERAL_NODE', true),
            array('ROLE_ACCESS_REDIRECTION', true),
            array('ROLE_ACCESS_TREE_NODE', true),
            array('ROLE_ADMIN', false),
            array('ROLE_USER', false),
            array('ROLE_FROM_PUBLISHED_TO_DRAFT', false),
        );
    }

    /**
     * @param array  $roles
     * @param string $accessResponse
     * @param bool   $superAdmin
     *
     * @dataProvider provideRoleAndAccess
     */
    public function testVote($roles, $accessResponse, $superAdmin = false)
    {
        $siteId1 = '1';
        $siteId2 = '2';
        $role1 = 'ROLE_ACCESS_1';
        $role2 = 'ROLE_ACCESS_2';
        $site1 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site1)->getSiteId()->thenReturn($siteId1);
        $site2 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site2)->getSiteId()->thenReturn($siteId2);

        $group1 = Phake::mock('OpenOrchestra\BackofficeBundle\Model\GroupInterface');
        Phake::when($group1)->getSite()->thenReturn($site1);
        Phake::when($group1)->getRoles()->thenReturn(array($role1));
        $group2 = Phake::mock('OpenOrchestra\BackofficeBundle\Model\GroupInterface');
        Phake::when($group2)->getSite()->thenReturn($site2);
        Phake::when($group2)->getRoles()->thenReturn(array($role2));

        if ($superAdmin) {
            $user = Phake::mock('FOS\UserBundle\Model\UserInterface');
        } else {
            $user = Phake::mock('FOS\UserBundle\Model\GroupableInterface');
        }
        Phake::when($user)->getGroups()->thenReturn(array($group1, $group2));
        Phake::when($user)->isSuperAdmin()->thenReturn($superAdmin);

        $token = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        Phake::when($token)->getUser()->thenReturn($user);

        Phake::when($this->contextManager)->getCurrentSiteId()->thenReturn($siteId1);

        $this->assertSame($accessResponse, $this->voter->vote($token, null, $roles));
    }

    /**
     * @return array
     */
    public function provideRoleAndAccess()
    {
        return array(
            array(array('ROLE_ACCESS_1'), VoterInterface::ACCESS_GRANTED),
            array(array('ROLE_ACCESS_2'), VoterInterface::ACCESS_DENIED),
            array(array('ROLE_ACCESS_3'), VoterInterface::ACCESS_DENIED),
            array(array('ROLE_USER'), VoterInterface::ACCESS_ABSTAIN),
            array(array('ROLE_USER', 'ROLE_ACCESS_1'), VoterInterface::ACCESS_GRANTED),
            array(array('ROLE_ACCESS_1', 'ROLE_USER'), VoterInterface::ACCESS_GRANTED),
            array(array('ROLE_ACCESS_2', 'ROLE_USER'), VoterInterface::ACCESS_DENIED),
            array(array('ROLE_USER', 'ROLE_ACCESS_2'), VoterInterface::ACCESS_DENIED),
            array(array('ROLE_ACCESS_1', 'ROLE_ACCESS_2'), VoterInterface::ACCESS_GRANTED),
            array(array('ROLE_ACCESS_2', 'ROLE_ACCESS_1'), VoterInterface::ACCESS_GRANTED),
            array(array('ROLE_USER'), VoterInterface::ACCESS_GRANTED, true),
            array(array('ROLE_USER', 'ROLE_ACCESS_2'), VoterInterface::ACCESS_GRANTED, true),
        );
    }

    /**
     * @param array  $roles
     * @param string $accessResponse
     *
     * @dataProvider provideRoleAndAccessWithNoSiteInRole
     */
    public function testVoteWithNoSiteInRole($roles, $accessResponse)
    {
        $siteId1 = '1';
        $siteId2 = '2';
        $role1 = 'ROLE_ACCESS_1';
        $role2 = 'ROLE_ACCESS_2';
        $site1 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site1)->getSiteId()->thenReturn($siteId1);
        $site2 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site2)->getSiteId()->thenReturn($siteId2);
        Phake::when($this->siteRepository)->findByDeleted(false)->thenReturn(array($site1, $site2));

        $group1 = Phake::mock('OpenOrchestra\BackofficeBundle\Model\GroupInterface');
        Phake::when($group1)->getRoles()->thenReturn(array($role1));
        $group2 = Phake::mock('OpenOrchestra\BackofficeBundle\Model\GroupInterface');
        Phake::when($group2)->getRoles()->thenReturn(array($role2));

        $user = Phake::mock('FOS\UserBundle\Model\GroupableInterface');

        Phake::when($user)->getGroups()->thenReturn(array($group1, $group2));

        $token = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        Phake::when($token)->getUser()->thenReturn($user);

        Phake::when($this->contextManager)->getCurrentSiteId()->thenReturn($siteId1);

        $this->assertSame($accessResponse, $this->voter->vote($token, null, $roles));
    }

    /**
     * @return array
     */
    public function provideRoleAndAccessWithNoSiteInRole()
    {
        return array(
            array(array('ROLE_ACCESS_1'), VoterInterface::ACCESS_GRANTED),
            array(array('ROLE_ACCESS_2'), VoterInterface::ACCESS_GRANTED),
            array(array('ROLE_ACCESS_3'), VoterInterface::ACCESS_DENIED),
            array(array('ROLE_USER'), VoterInterface::ACCESS_ABSTAIN),
        );
    }
}
