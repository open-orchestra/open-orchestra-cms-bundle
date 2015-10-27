<?php

namespace OpenOrchestra\BackofficeBundle\Tests\DependencyInjection\Compiler;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\ContentTypeForContentPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\GeneralNodesPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\GSTreeTemplatePanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TopMenuPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeTemplatePanelStrategy;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\RoleCompilerPass;
use Phake;

/**
 * Class RoleCompilerPassTest
 */
class RoleCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RoleCompilerPass
     */
    protected $compiler;

    protected $containerBuilder;
    protected $serviceDefinition;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->serviceDefinition = Phake::mock('Symfony\Component\DependencyInjection\Definition');
        $this->containerBuilder = Phake::mock('Symfony\Component\DependencyInjection\ContainerBuilder');
        Phake::when($this->containerBuilder)->hasDefinition(Phake::anyParameters())->thenReturn(false);
        Phake::when($this->containerBuilder)->getDefinition(Phake::anyParameters())->thenReturn($this->serviceDefinition);

        $this->compiler = new RoleCompilerPass();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface', $this->compiler);
    }

    /**
     * Test with no service
     */
    public function testProcessWithNoService()
    {
        $this->compiler->process($this->containerBuilder);

        Phake::verify($this->serviceDefinition, Phake::never())->addMethodCall(Phake::anyParameters());
    }

    /**
     * Test add all services
     */
    public function testWithServices()
    {
        $roles = array(
            ContentTypeForContentPanelStrategy::ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT,
            AdministrationPanelStrategy::ROLE_ACCESS_CONTENT_TYPE,
            AdministrationPanelStrategy::ROLE_ACCESS_REDIRECTION,
            AdministrationPanelStrategy::ROLE_ACCESS_API_CLIENT,
            AdministrationPanelStrategy::ROLE_ACCESS_CREATE_API_CLIENT,
            AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_API_CLIENT,
            AdministrationPanelStrategy::ROLE_ACCESS_DELETE_API_CLIENT,
            TreeTemplatePanelStrategy::ROLE_ACCESS_TREE_TEMPLATE,
            GeneralNodesPanelStrategy::ROLE_ACCESS_GENERAL_NODE,
            AdministrationPanelStrategy::ROLE_ACCESS_KEYWORD,
            AdministrationPanelStrategy::ROLE_ACCESS_DELETED,
            AdministrationPanelStrategy::ROLE_ACCESS_STATUS,
            AdministrationPanelStrategy::ROLE_ACCESS_THEME,
            AdministrationPanelStrategy::ROLE_ACCESS_GROUP,
            AdministrationPanelStrategy::ROLE_ACCESS_ROLE,
            TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE,
            TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE,
            TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE,
            TreeNodesPanelStrategy::ROLE_ACCESS_DELETE_NODE,
            AdministrationPanelStrategy::ROLE_ACCESS_SITE,
            AdministrationPanelStrategy::ROLE_ACCESS_CREATE_SITE,
            AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_SITE,
            AdministrationPanelStrategy::ROLE_ACCESS_DELETE_SITE,
        );

        Phake::when($this->containerBuilder)->hasDefinition(Phake::anyParameters())->thenReturn(true);

        $this->compiler->process($this->containerBuilder);

        foreach ($roles as $role) {
            Phake::verify($this->serviceDefinition)->addMethodCall('addRole', array($role));
        }
    }
}
