<?php

namespace OpenOrchestra\Backoffice\Tests\Collector;

use OpenOrchestra\Backoffice\Collector\BackofficeRoleCollector;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class BackofficeRoleCollectorTest
 */
class BackofficeRoleCollectorTest extends AbstractBaseTestCase
{
    protected $translator;
    protected $multiLanguagesChoiceManager;
    protected $fakeTrans = 'fakeTrans';

    /**
     * Set up the test
     */
    public function setUp()
    {
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
            new BackofficeRoleCollector($this->translator, $this->multiLanguagesChoiceManager)
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
        $collector = new BackofficeRoleCollector($this->translator, $this->multiLanguagesChoiceManager);
        foreach ($newRoles as $newRole) {
            $collector->addRole($newRole);
        }

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
     * @param array  $roles
     * @param string $roleToCheck
     * @param bool   $answer
     *
     * @dataProvider provideHasRoleData
     */
    public function testHasTest(array $roles, $roleToCheck, $answer)
    {
        $collector = new BackofficeRoleCollector($this->translator, $this->multiLanguagesChoiceManager);
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
