<?php

namespace OpenOrchestra\GroupBundle\Tests\AuthorizeStatusChange\Strategies;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\GroupBundle\AuthorizeStatusChange\Strategies\GroupStrategy;
use Phake;

/**
 * Test GroupStrategyTest
 */
class GroupStrategyTest extends AbstractBaseTestCase
{
    /**
     * @var GroupStrategy
     */
    protected $strategy;

    protected $authorizationChecker;
    protected $roleRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $this->roleRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface');

        $this->strategy = new GroupStrategy($this->authorizationChecker, $this->roleRepository);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('OpenOrchestra\Backoffice\AuthorizeStatusChange\AuthorizeStatusChangeInterface', $this->strategy);
    }

    /**
     * Test name
     */
    public function testGetName()
    {
        $this->assertSame('group', $this->strategy->getName());
    }

    /**
     * @param bool       $response
     * @param bool       $isGranted
     * @param mixed|null $role
     *
     * @dataProvider provideRoleAndCheckAndResponse
     */
    public function testIsGranted($response, $isGranted, $role = null)
    {
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $statusable = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        Phake::when($statusable)->getStatus()->thenReturn($status);
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn($isGranted);
        Phake::when($this->roleRepository)->findOneByFromStatusAndToStatus(Phake::anyParameters())->thenReturn($role);

        $this->assertSame($response, $this->strategy->isGranted($statusable, $status));
    }

    /**
     * @return array
     */
    public function provideRoleAndCheckAndResponse()
    {
        $role = Phake::mock('OpenOrchestra\ModelInterface\Model\RoleInterface');

        return array(
            array(true, true, $role),
            array(false, false, $role),
            array(true, true),
            array(true, false),
        );
    }
}
