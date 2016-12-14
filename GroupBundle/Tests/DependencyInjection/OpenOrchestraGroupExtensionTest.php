<?php
namespace OpenOrchestra\GroupBundle\Tests\DependencyInjection;

use OpenOrchestra\GroupBundle\DependencyInjection\OpenOrchestraGroupExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use \OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;

/**
 * Class OpenOrchestraGroupExtensionTest
 */
class OpenOrchestraGroupExtensionTest extends AbstractBaseTestCase
{
    /**
     * @var ContainerBuilder
     */
    protected $emptyContainer;
    /**
     * @var ContainerBuilder
     */
    protected $valueContainer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->emptyContainer = $this->loadContainerFromFile('empty');
        $this->valueContainer = $this->loadContainerFromFile('value');
    }

    /**
     * Test facades config
     *
     * @param string $parameter
     * @param string $facadeClass
     *
     * @dataProvider provideFacadesConfig
     */
    public function testDefaultFacadesConfig($parameter, $facadeClass)
    {
        $this->assertEquals('OpenOrchestra\GroupBundle\Facade\\'.$facadeClass, $this->emptyContainer->getParameter('open_orchestra_group.facade.'.$parameter.'.class'));
        $this->assertEquals('FacadeClass', $this->valueContainer->getParameter('open_orchestra_group.facade.'.$parameter.'.class'));
    }

    /**
     * @return array
     */
    public function provideFacadesConfig()
    {
        return array(
            array('group', 'GroupFacade'),
            array('group_collection', 'GroupCollectionFacade'),
        );
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
        $container->registerExtension(new OpenOrchestraGroupExtension());

        $locator = new FileLocator(__DIR__ . '/Fixtures/config/');
        $loader = new YamlFileLoader($container, $locator);
        $loader->load($file . '.yml');
        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        return $container;
    }
}
