<?php

namespace PHPOrchestra\CMSBundle\Test\Routing;

use Phake;
use PHPOrchestra\CMSBundle\Routing\PhpOrchestraRouter;
use Symfony\Component\Routing\RouteCollection;

/**
 * Tests of PhpOrchestraUrlRouter
 *
 * @author Noel GILAIN <noel.gilain@businessdecision.com>
 */
class PhpOrchestraRouterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $cacheService = Phake::mock('PHPOrchestra\CMSBundle\Cache\CacheManagerInterface');
        $nodeRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\NodeRepository');

        $mockRoutingLoader = Phake::mock('Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader');
        Phake::when($mockRoutingLoader)->load(Phake::anyParameters())->thenReturn(new RouteCollection());

        $container = Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        Phake::when($container)->get('routing.loader')->thenReturn($mockRoutingLoader);
        Phake::when($container)->get('php_orchestra_model.repository.node')->thenReturn($nodeRepository);
        Phake::when($container)->get('php_orchestra_cms.cache_manager')->thenReturn($cacheService);

        $this->router = new PhpOrchestraRouter(
            $container,
            null,
            array(
                'generator_class' => 'PHPOrchestra\CMSBundle\Routing\PhpOrchestraUrlGenerator',
                'generator_base_class' => 'PHPOrchestra\CMSBundle\Routing\PhpOrchestraUrlGenerator',
                'matcher_class' => 'PHPOrchestra\CMSBundle\Routing\PhpOrchestraUrlMatcher',
                'matcher_base_class' => 'PHPOrchestra\CMSBundle\Routing\PhpOrchestraUrlMatcher'
            )
        );
    }
    
    public function testGetMatcher()
    {
        $this->assertInstanceOf(
            'PHPOrchestra\\CMSBundle\\Routing\\PhpOrchestraUrlMatcher',
            $this->router->getMatcher()
        );
    }
    
    public function testGetGenerator()
    {
        $this->assertInstanceOf(
            'PHPOrchestra\\CMSBundle\\Routing\\PhpOrchestraUrlGenerator',
            $this->router->getGenerator()
        );
    }
}
