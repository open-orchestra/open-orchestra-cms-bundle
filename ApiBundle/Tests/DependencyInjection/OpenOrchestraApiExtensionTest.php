<?php

use OpenOrchestra\ApiBundle\DependencyInjection\OpenOrchestraApiExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;

/**
 * Class OpenOrchestraApiExtensionTest
 */
class OpenOrchestraApiExtensionTest extends AbstractBaseTestCase
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
        $this->assertEquals('OpenOrchestra\ApiBundle\Facade\\'.$facadeClass, $this->emptyContainer->getParameter('open_orchestra_api.facade.'.$parameter.'.class'));
        $this->assertEquals('FacadeClass', $this->valueContainer->getParameter('open_orchestra_api.facade.'.$parameter.'.class'));
    }

    /**
     * @return array
     */
    public function provideFacadesConfig()
    {
        return array(
            array('api_client', 'ApiClientFacade'),
            array('api_client_collection', 'ApiClientCollectionFacade'),
            array('area', 'AreaFacade'),
            array('block', 'BlockFacade'),
            array('block_collection', 'BlockCollectionFacade'),
            array('content', 'ContentFacade'),
            array('content_attribute', 'ContentAttributeFacade'),
            array('content_collection', 'ContentCollectionFacade'),
            array('content_type', 'ContentTypeFacade'),
            array('content_type_collection', 'ContentTypeCollectionFacade'),
            array('field_type', 'FieldTypeFacade'),
            array('keyword', 'KeywordFacade'),
            array('keyword_collection', 'KeywordCollectionFacade'),
            array('link', 'LinkFacade'),
            array('node', 'NodeFacade'),
            array('node_collection', 'NodeCollectionFacade'),
            array('node_tree', 'NodeTreeFacade'),
            array('redirection', 'RedirectionFacade'),
            array('redirection_collection', 'RedirectionCollectionFacade'),
            array('site', 'SiteFacade'),
            array('site_collection', 'SiteCollectionFacade'),
            array('template', 'TemplateFacade'),
            array('trash_item', 'TrashItemFacade'),
            array('trash_item_collection', 'TrashItemCollectionFacade')
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
        $container->registerExtension(new OpenOrchestraApiExtension());

        $locator = new FileLocator(__DIR__ . '/Fixtures/config/');
        $loader = new YamlFileLoader($container, $locator);
        $loader->load($file . '.yml');
        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        return $container;
    }
}
