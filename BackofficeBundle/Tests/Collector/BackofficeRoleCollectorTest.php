<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Collector;

use OpenOrchestra\Backoffice\Collector\BackofficeRoleCollector;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\ContentTypeForContentPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\GeneralNodesPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use Phake;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class BackofficeRoleCollectorTest
 */
class BackofficeRoleCollectorTest extends \PHPUnit_Framework_TestCase
{
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
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(
            'OpenOrchestra\Backoffice\Collector\RoleCollectorInterface',
            new BackofficeRoleCollector($this->roleRepository, $this->translator, $this->translationChoiceManager, false)
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
        $collector = new BackofficeRoleCollector($this->roleRepository, $this->translator, $this->translationChoiceManager, false);
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

        $collector = new BackofficeRoleCollector($this->roleRepository, $this->translator, $this->translationChoiceManager, true);

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
        $collector = new BackofficeRoleCollector($this->roleRepository, $this->translator, $this->translationChoiceManager, false);
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
            array(array(TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE), 'node', array(TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE => $this->fakeTrans)),
            array(array(GeneralNodesPanelStrategy::ROLE_ACCESS_TREE_GENERAL_NODE), 'node', array()),
            array(array(TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE), 'template', array()),
            array(array(TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE), 'content', array()),
            array(array(
                TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE,
                TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE,
                TreeNodesPanelStrategy::ROLE_ACCESS_DELETE_NODE,
                TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE,
                ContentTypeForContentPanelStrategy::ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT,
                ContentTypeForContentPanelStrategy::ROLE_ACCESS_CREATE_CONTENT_TYPE_FOR_CONTENT,
                ContentTypeForContentPanelStrategy::ROLE_ACCESS_UPDATE_CONTENT_TYPE_FOR_CONTENT,
                GeneralNodesPanelStrategy::ROLE_ACCESS_TREE_GENERAL_NODE,
                GeneralNodesPanelStrategy::ROLE_ACCESS_UPDATE_GENERAL_NODE,
            ), 'node', array(
                TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE => $this->fakeTrans,
                TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE => $this->fakeTrans,
                TreeNodesPanelStrategy::ROLE_ACCESS_DELETE_NODE => $this->fakeTrans,
                TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE => $this->fakeTrans,
            )),
            array(array(
                TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE,
                TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE,
                TreeNodesPanelStrategy::ROLE_ACCESS_DELETE_NODE,
                TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE,
                GeneralNodesPanelStrategy::ROLE_ACCESS_TREE_GENERAL_NODE,
                GeneralNodesPanelStrategy::ROLE_ACCESS_UPDATE_GENERAL_NODE,
            ), 'general_node', array(
                GeneralNodesPanelStrategy::ROLE_ACCESS_TREE_GENERAL_NODE  => $this->fakeTrans,
                GeneralNodesPanelStrategy::ROLE_ACCESS_UPDATE_GENERAL_NODE  => $this->fakeTrans,
            )),
            array(array(
                TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE,
                TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE,
                TreeNodesPanelStrategy::ROLE_ACCESS_DELETE_NODE,
                TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE,
                ContentTypeForContentPanelStrategy::ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT,
                ContentTypeForContentPanelStrategy::ROLE_ACCESS_CREATE_CONTENT_TYPE_FOR_CONTENT,
                ContentTypeForContentPanelStrategy::ROLE_ACCESS_UPDATE_CONTENT_TYPE_FOR_CONTENT,
                AdministrationPanelStrategy::ROLE_ACCESS_CONTENT_TYPE,
                AdministrationPanelStrategy::ROLE_ACCESS_CREATE_CONTENT_TYPE,
            ), 'content_type_for_content', array(
                ContentTypeForContentPanelStrategy::ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT => $this->fakeTrans,
                ContentTypeForContentPanelStrategy::ROLE_ACCESS_CREATE_CONTENT_TYPE_FOR_CONTENT => $this->fakeTrans,
                ContentTypeForContentPanelStrategy::ROLE_ACCESS_UPDATE_CONTENT_TYPE_FOR_CONTENT => $this->fakeTrans,
            )),
            array(array(
                ContentTypeForContentPanelStrategy::ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT,
                ContentTypeForContentPanelStrategy::ROLE_ACCESS_CREATE_CONTENT_TYPE_FOR_CONTENT,
                ContentTypeForContentPanelStrategy::ROLE_ACCESS_UPDATE_CONTENT_TYPE_FOR_CONTENT,
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
        $collector = new BackofficeRoleCollector($this->roleRepository, $this->translator, $this->translationChoiceManager, false);
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
