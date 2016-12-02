<?php

namespace OpenOrchestra\Backoffice\Tests\Collector;

use OpenOrchestra\Backoffice\Collector\BackofficeRoleCollector;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class BackofficeRoleCollectorTest
 */
class BackofficeRoleCollectorTest extends AbstractBaseTestCase
{
    protected $roleRepository;
    protected $translator;
    protected $multiLanguagesChoiceManager;
    protected $fakeTrans = 'fakeTrans';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->roleRepository = \Phake::mock('OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface');
        $this->translator = \Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $this->multiLanguagesChoiceManager = Phake::mock('OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface');
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn($this->fakeTrans);
        Phake::when($this->multiLanguagesChoiceManager)->choose(Phake::anyParameters())->thenReturn($this->fakeTrans);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(
            'OpenOrchestra\Backoffice\Collector\RoleCollectorInterface',
            new BackofficeRoleCollector($this->roleRepository, $this->translator, $this->multiLanguagesChoiceManager, false)
        );
    }
    /**
     * @param array $newRoles
     * @param array $expectedRoles
     *
     * @dataProvider provideRolesAndExpected
     */
    public function testAddAndGetRoles(array $newRoles, array $expectedRoles)
    {
        $collector = new BackofficeRoleCollector($this->roleRepository, $this->translator, $this->multiLanguagesChoiceManager, false);
        foreach ($newRoles as $newRole) {
            $collector->addRole($newRole);
        }

        $this->assertSame($expectedRoles, $collector->getRoles());
    }

    /**
     * @param array $newRoles
     * @param array $expectedRoles
     *
     * @dataProvider provideRolesAndExpected
     */
    public function testLoadWorkflowRole(array $newRoles, array $expectedRoles)
    {
        $roles = new ArrayCollection();

        foreach ($newRoles as $newRole) {
            $role = Phake::mock('OpenOrchestra\ModelInterface\Model\RoleInterface');
            Phake::when($role)->getName()->thenReturn($newRole);
            Phake::when($role)->getDescriptions()->thenReturn(array());
            $roles->add($role);
        }

        Phake::when($this->roleRepository)->findWorkflowRole()->thenReturn($roles);

        $collector = new BackofficeRoleCollector($this->roleRepository, $this->translator, $this->multiLanguagesChoiceManager, true);

        $this->assertSame($expectedRoles, $collector->getRoles());
    }

    /**
     * @return array
     */
    public function provideRolesAndExpected()
    {
        return array(
            array(array(), array()),
            array(array('foo'), array('foo' => $this->fakeTrans)),
            array(array('foo', 'foo'), array('foo' => $this->fakeTrans)),
            array(array('foo', 'bar'), array('foo' => $this->fakeTrans, 'bar' => $this->fakeTrans)),
        );
    }

    /**
     * @param array  $newRoles
     * @param string $type
     * @param array  $expectedRoles
     *
     * @dataProvider provideRoleAndTypeAndExpected
     */
    public function testGetRolesByType(array $newRoles, $type, array $expectedRoles)
    {
        $collector = new BackofficeRoleCollector($this->roleRepository, $this->translator, $this->multiLanguagesChoiceManager, false);
        foreach ($newRoles as $newRole) {
            $collector->addRole($newRole);
        }

        $this->assertSame($expectedRoles, $collector->getRolesByType($type));
    }

    /**
     * @return array
     */
    public function provideRoleAndTypeAndExpected()
    {
        return array(
            array(array(
                AdministrationPanelStrategy::ROLE_ACCESS_CONTENT_TYPE,
                AdministrationPanelStrategy::ROLE_ACCESS_CREATE_CONTENT_TYPE,
            ), 'content_type', array(
                AdministrationPanelStrategy::ROLE_ACCESS_CONTENT_TYPE  => $this->fakeTrans,
                AdministrationPanelStrategy::ROLE_ACCESS_CREATE_CONTENT_TYPE  => $this->fakeTrans,
            )),
        );
    }

    /**
     * @param array  $roles
     * @param string $roleToCheck
     * @param bool   $answer
     *
     * @dataProvider provideHasRoleData
     */
    public function testHasTest(array $roles, $roleToCheck, $answer)
    {
        $collector = new BackofficeRoleCollector($this->roleRepository, $this->translator, $this->multiLanguagesChoiceManager, false);
        foreach ($roles as $newRole) {
            $collector->addRole($newRole);
        }

        $this->assertSame($answer, $collector->hasRole($roleToCheck));
    }

    /**
     * @return array
     */
    public function provideHasRoleData()
    {
        return array(
            array(array('role_foo'), 'foo', false),
            array(array('role_foo'), 'role_foo', true),
            array(array('role_foo', 'role_bar'), 'role_foo', true),
        );
    }
}
