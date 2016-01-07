<?php

use OpenOrchestra\ApiBundle\DependencyInjection\OpenOrchestraApiExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use \OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;

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
            array('datatable_translation', 'DatatableTranslationFacade'),
            array('field_type', 'FieldTypeFacade'),
            array('group', 'GroupFacade'),
            array('group_collection', 'GroupCollectionFacade'),
            array('keyword', 'KeywordFacade'),
            array('keyword_collection', 'KeywordCollectionFacade'),
            array('link', 'LinkFacade'),
            array('node', 'NodeFacade'),
            array('node_collection', 'NodeCollectionFacade'),
            array('node_group_role', 'NodeGroupRoleFacade'),
            array('node_tree', 'NodeTreeFacade'),
            array('redirection', 'RedirectionFacade'),
            array('redirection_collection', 'RedirectionCollectionFacade'),
            array('role', 'RoleFacade'),
            array('role_collection', 'RoleCollectionFacade'),
            array('role_string', 'RoleFacade'),
            array('role_string_collection', 'RoleCollectionFacade'),
            array('site', 'SiteFacade'),
            array('site_collection', 'SiteCollectionFacade'),
            array('status', 'StatusFacade'),
            array('status_collection', 'StatusCollectionFacade'),
            array('template', 'TemplateFacade'),
            array('theme', 'ThemeFacade'),
            array('theme_collection', 'ThemeCollectionFacade'),
            array('trash_item', 'TrashItemFacade'),
            array('trash_item_collection', 'TrashItemCollectionFacade'),
            array('translation', 'TranslationFacade'),
            array('ui_model', 'UiModelFacade'),
            array('widget', 'WidgetFacade'),
            array('widget_collection', 'WidgetCollectionFacade'),
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
