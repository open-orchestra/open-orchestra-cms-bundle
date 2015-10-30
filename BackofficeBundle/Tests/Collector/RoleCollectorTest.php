<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Collector;

use OpenOrchestra\Backoffice\Collector\RoleCollector;
use Phake;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class RoleCollectorTest
 */
class RoleCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RoleCollector
     */
    protected $collector;
    protected $roleRepository;
    protected $translator;
    protected $translationChoiceManager;

    protected $fakeTrans = 'fakeTrans';
    /**
     * Set up the test
     */
    public function setUp()
    {

        $this->roleRepository = \Phake::mock('OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface');
        $this->translator = \Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $this->translationChoiceManager = \Phake::mock('OpenOrchestra\ModelInterface\Manager\TranslationChoiceManagerInterface');
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn($this->fakeTrans);
        Phake::when($this->translationChoiceManager)->choose(Phake::anyParameters())->thenReturn($this->fakeTrans);

    }

    /**
     * @param array $newRoles
     * @param array $expectedRoles
     *
     * @dataProvider provideRolesAndExpected
     */
    public function testAddAndGetRoles(array $newRoles, array $expectedRoles)
    {
        $collector = new RoleCollector($this->roleRepository, $this->translator, $this->translationChoiceManager, false);
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
            Phake::when($role)->getDescriptions()->thenReturn(Phake::mock('Doctrine\Common\Collections\Collection'));
            $roles->add($role);
        }

        Phake::when($this->roleRepository)->findWorkflowRole()->thenReturn($roles);

        $collector = new RoleCollector($this->roleRepository, $this->translator, $this->translationChoiceManager, true);

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

}
