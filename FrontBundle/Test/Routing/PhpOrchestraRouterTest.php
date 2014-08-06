<?php

namespace PHPOrchestra\FrontBundle\Test\Routing;

use Phake;
use PHPOrchestra\FrontBundle\Routing\PhpOrchestraRouter;
use Symfony\Component\Routing\RouteCollection;

/**
 * Tests of PhpOrchestraUrlRouter
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
                'generator_class' => 'PHPOrchestra\FrontBundle\Routing\PhpOrchestraUrlGenerator',
                'generator_base_class' => 'PHPOrchestra\FrontBundle\Routing\PhpOrchestraUrlGenerator',
                'matcher_class' => 'PHPOrchestra\FrontBundle\Routing\PhpOrchestraUrlMatcher',
                'matcher_base_class' => 'PHPOrchestra\FrontBundle\Routing\PhpOrchestraUrlMatcher'
            )
        );
    }
    
    public function testGetMatcher()
    {
        $this->assertInstanceOf(
            'PHPOrchestra\\FrontBundle\\Routing\\PhpOrchestraUrlMatcher',
            $this->router->getMatcher()
        );
    }
    
    public function testGetGenerator()
    {
        $this->assertInstanceOf(
            'PHPOrchestra\\FrontBundle\\Routing\\PhpOrchestraUrlGenerator',
            $this->router->getGenerator()
        );
    }
}
