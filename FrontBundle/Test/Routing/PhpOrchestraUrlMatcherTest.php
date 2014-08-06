<?php

namespace PHPOrchestra\FrontBundle\Test\Routing;

use Phake;
use PHPOrchestra\FrontBundle\Routing\PhpOrchestraUrlMatcher;
use Symfony\Component\Routing\RouteCollection;

/**
 * Tests of PhpOrchestraUrlMatcher
 */
class PhpOrchestraUrlMatcherTest extends \PHPUnit_Framework_TestCase
{
    protected $node;
    protected $cache;
    protected $matcher;
    protected $context;
    protected $cacheService;
    protected $nodeRepository;

    /**
     * Set up a fake database for Route testing
     */
    public function setUp()
    {
        $this->context = Phake::mock('Symfony\Component\Routing\RequestContext');

        $this->cacheService = Phake::mock('PHPOrchestra\BaseBundle\Cache\CacheManagerInterface');

        $routes = new RouteCollection();

        $this->node = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        $this->nodeRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\NodeRepository');

        $this->matcher = new PhpOrchestraUrlMatcher(
            $routes,
            $this->context,
            $this->nodeRepository,
            $this->cacheService
        );
    }

    /**
     * @dataProvider matchDataProvider
     */
    public function testMatch($route, $controller, $nodeId, $moduleParams, $pathinfo)
    {
        Phake::when($this->nodeRepository)->findOneBy(array(
            'alias' => 'module',
            'parentId' => '3'
        ))->thenReturn($this->node);
        Phake::when($this->nodeRepository)->findOneBy(array(
            'alias' => 'test',
            'parentId' => 'root'
        ))->thenReturn($this->node);

        Phake::when($this->node)->getNodeId()->thenReturn($nodeId);
        Phake::when($this->node)->getNodeType()->thenReturn('node');

        $parameters = $this->matcher->match($pathinfo);

        $this->assertEquals($route, $parameters['_route']);
        $this->assertEquals($controller, $parameters['_controller']);
        $this->assertEquals($nodeId, $parameters['nodeId']);
        if ($moduleParams != '') {
            $this->assertEquals($moduleParams, $parameters['module_parameters']);
        }
        Phake::verify($this->cacheService)->set(Phake::anyParameters());
        Phake::verify($this->cacheService)->get(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function matchDataProvider()
    {
        return array(
            array(
                'php_orchestra_front_node',
                'PHPOrchestra\\FrontBundle\\Controller\\NodeController::showAction',
                2,
                '',
                '/test/'
            ),
            array(
                'php_orchestra_front_node',
                'PHPOrchestra\\FrontBundle\\Controller\\NodeController::showAction',
                3,
                array('param1', 'param2'),
                '/test/module/param1/param2'
            ),
            array(
                'php_orchestra_front_node',
                'PHPOrchestra\\FrontBundle\\Controller\\NodeController::showAction',
                3,
                array(),
                '/test/module'
            ),
        );
    }
}
