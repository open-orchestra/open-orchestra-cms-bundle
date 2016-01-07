<?php

namespace OpenOrchestra\Backoffice\Tests\Collector;

use OpenOrchestra\Backoffice\Collector\FrontRoleCollector;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\UserBundle\Model\UserInterface;
use Phake;

/**
 * Test FrontRoleCollectorTest
 */
class FrontRoleCollectorTest extends AbstractBaseTestCase
{
    /**
     * @var FrontRoleCollector
     */
    protected $collector;

    protected $translator;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface', $this->translator);
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn('translated_role');

        $this->collector = new FrontRoleCollector($this->translator);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('OpenOrchestra\Backoffice\Collector\RoleCollectorInterface', $this->collector);
    }

    /**
     * @param string $role
     *
     * @dataProvider provideRoles
     */
    public function testAddHasAndGetRole($role, $translatedRole)
    {
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn($translatedRole);
        $this->collector->addRole($role);

        $this->assertTrue($this->collector->hasRole($role));
        $this->assertContains($translatedRole, $this->collector->getRoles());
        $this->assertArrayHasKey($role, $this->collector->getRoles());
        Phake::verify($this->translator)->trans('open_orchestra_role.' . strtolower($role), array(), 'role');
    }

    /**
     * @return array
     */
    public function provideRoles()
    {
        return array(
            array('bar', 'baz'),
            array('foo', 'baz'),
            array('bar', 'bar'),
            array('foo', 'bar'),
            array('BAR', 'baz'),
            array('FoO', 'baz'),
            array('bAR', 'bar'),
        );
    }

    /**
     * Test collector has ROLE_USER
     */
    public function testHasRoleUser()
    {
        $this->assertTrue($this->collector->hasRole(UserInterface::ROLE_DEFAULT));
        $this->assertSame(array(UserInterface::ROLE_DEFAULT => 'translated_role'), $this->collector->getRoles());
    }
}
