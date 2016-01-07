<?php

namespace OpenOrchestra\BaseApiBundle\DependencyInjection;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelLogBundle\DependencyInjection\OpenOrchestraModelLogExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class OpenOrchestraModelLogExtensionTest
 */
class OpenOrchestraModelLogExtensionTest extends AbstractBaseTestCase
{
    /**
     * Test default config
     */
    public function testDefaultConfig()
    {
        $class = 'OpenOrchestra\ModelLogBundle\Document\Log';
        $repository = 'OpenOrchestra\ModelLogBundle\Repository\LogRepository';
        $container = $this->loadContainerFromFile('empty');
        $this->assertEquals($class, $container->getParameter('open_orchestra_log.document.log.class'));
        $this->assertDefinition($container->getDefinition('open_orchestra_log.repository.log'), $class, $repository, true);
    }

    /**
     * Test with configuration
     */
    public function testConfigWithValue()
    {
        $container = $this->loadContainerFromFile('value');
        $this->assertEquals('FakeClassLog', $container->getParameter('open_orchestra_log.document.log.class'));
        $this->assertDefinition($container->getDefinition('open_orchestra_log.repository.log'), 'FakeClassLog', 'FakeRepositoryLog');
    }

    /**
     * @param Definition $definition
     * @param string     $class
     * @param string     $repository
     * @param bool|false $filterType
     */
    private function assertDefinition(Definition $definition, $class, $repository, $filterType = false)
    {
        $this->assertSame($definition->getClass(), $repository);
        $factory = $definition->getFactory();
        $this->assertSame($factory[1], "getRepository");
        $this->assertSame($definition->getArgument(0), $class);
        $this->assertTrue($definition->hasMethodCall('setAggregationQueryBuilder'));
        if ($filterType) {
            $this->assertTrue($definition->hasMethodCall('setFilterTypeManager'));
        }
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
        $container->registerExtension(new OpenOrchestraModelLogExtension());

        $locator = new FileLocator(__DIR__ . '/Fixtures/config/');
        $loader = new YamlFileLoader($container, $locator);
        $loader->load($file . '.yml');
        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        return $container;
    }
}
