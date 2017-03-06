<?php

namespace OpenOrchestra\WorkflowAdminBundle\DependencyInjection;

use OpenOrchestra\WorkflowAdminBundle\DependencyInjection\OpenOrchestraWorkflowAdminExtension;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class OpenOrchestraWorkflowAdminExtensionTest
 */
class OpenOrchestraWorkflowAdminExtensionTest extends AbstractBaseTestCase
{
    /**
     * Test default config
     */
    public function testDefaultConfig()
    {
        $container = $this->loadContainerFromFile('empty');

        $defaultColor =  array(
            'turquoise' => 'open_orchestra_workflow_admin.form.status.color.turquoise',
            'green'     => 'open_orchestra_workflow_admin.form.status.color.green',
            'blue'      => 'open_orchestra_workflow_admin.form.status.color.blue',
            'purplue'   => 'open_orchestra_workflow_admin.form.status.color.purple',
            'dark-grey' => 'open_orchestra_workflow_admin.form.status.color.dark-grey',
            'yellow'    => 'open_orchestra_workflow_admin.form.status.color.yellow',
            'orange'    => 'open_orchestra_workflow_admin.form.status.color.orange',
            'red'       => 'open_orchestra_workflow_admin.form.status.color.red',
            'grey'      => 'open_orchestra_workflow_admin.form.status.color.grey',
        );

        $this->assertEquals($defaultColor, $container->getParameter('open_orchestra_workflow_admin.choice.available_color'));
   }

    /**
     * Test with configuration
     */
    public function testConfigWithValue()
    {
        $container = $this->loadContainerFromFile('value');

        $defaultColor =  array("fake_color" => "fake_translation_color");
        $this->assertEquals($defaultColor, $container->getParameter('open_orchestra_workflow_admin.choice.available_color'));
    }

    /**
     * @param string $file
     *
     * @return ContainerBuilder
     */
    private function loadContainerFromFile($file)
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', false);
        $container->setParameter('kernel.cache_dir', '/tmp');
        $container->setParameter('kernel.environment', 'prod');
        $container->registerExtension(new OpenOrchestraWorkflowAdminExtension());

        $locator = new FileLocator(__DIR__ . '/Fixtures/config/');
        $loader = new YamlFileLoader($container, $locator);
        $loader->load($file . '.yml');
        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        return $container;
    }
}
