<?php

namespace PHPOrchestra\FrontBundle\Routing;

use PHPOrchestra\CMSBundle\Cache\CacheManagerInterface;
use PHPOrchestra\ModelBundle\Repository\NodeRepository;
use Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Model\PHPOrchestraCMSBundle\Node;

/**
 * Dynamic routing based on url
 */
class PhpOrchestraUrlMatcher extends RedirectableUrlMatcher
{
    const PATH_PREFIX = 'router_pathinfo:';

    protected $nodeRepository;
    protected $cacheService;

    /**
     * Constructor
     * 
     * @param RouteCollection       $routes
     * @param RequestContext        $context
     * @param NodeRepository        $nodeRepository
     * @param CacheManagerInterface $cacheService
     */
    public function __construct(
        RouteCollection $routes,
        RequestContext $context,
        NodeRepository $nodeRepository,
        CacheManagerInterface $cacheService
    )
    {
        parent::__construct($routes, $context);
        $this->nodeRepository = $nodeRepository;
        $this->cacheService = $cacheService;
    }

    /**
     * Find a route for a given url
     *
     * Check first in cache,
     * Then with symfony basic behavior,
     * and finally with PhpOrchestra logic
     *
     * @param string $pathinfo
     *
     * @return array
     */
    public function match($pathinfo)
    {
        if ($this->getFromCache($pathinfo)) {
            $parameters = $this->getFromCache($pathinfo);
        } else {
            try {
                $parameters = parent::match($pathinfo);
            } catch (ResourceNotFoundException $e) {
                $parameters = $this->dynamicMatch($pathinfo);
            }
            
            $this->setToCache($pathinfo, $parameters);
        }
        
        return $parameters;
    }

    /**
     * try to find the node via its path
     *
     * @param string $pathinfo
     *
     * @return array
     * @throws ResourceNotFoundException
     */
    protected function dynamicMatch($pathinfo)
    {
        $slugs = explode('/', $pathinfo);
        $nodeId = Node::ROOT_NODE_ID;
        $nodeFound = false;
        $parameters = array();

        foreach ($slugs as $position => $slug) {
            
            if ($slug != '') {
                $node = $this->getNode($slug, $nodeId);

                if ($node) {
                    $nodeId = $node->getNodeId();
                    $nodeFound = true;

                    $parameters = array_slice($slugs, $position + 1);
                } elseif ($nodeFound) {
                    break;
                } else {
                    throw new ResourceNotFoundException();
                }
            }
        }
        
        return $this->getModuleRoute($nodeId, $parameters);
    }

    /**
     * Get the route parameters from cache if already set
     * 
     * @param string $pathinfo
     *
     * @return array
     */
    protected function getFromCache($pathinfo)
    {
        $parameters = $this->cacheService->get(self::PATH_PREFIX . $pathinfo);
        
        if (isset($parameters['module_parameters'])) {
            $parameters['module_parameters'] = unserialize($parameters['module_parameters']);
        }
        
        return $parameters;
    }

    /**
     * Set route parameters to cache for pathinfo
     * 
     * @param string   $pathinfo
     * @param string[] $routeParameters
     *
     * @return mixed
     */
    protected function setToCache($pathinfo, $routeParameters)
    {
        if (isset($routeParameters['module_parameters'])) {
            $routeParameters['module_parameters'] = serialize($routeParameters['module_parameters']);
        }
        
        return $this->cacheService->set(self::PATH_PREFIX . $pathinfo, $routeParameters);
    }

    /**
     * Route parameters for module page
     * ie with customs parameters at the end of url
     *
     * @param string $nodeId
     * @param array  $parameters
     *
     * @return array
     */
    protected function getModuleRoute($nodeId, $parameters = array())
    {
        return array(
             "_route" => "php_orchestra_front_node",
             "_controller" => 'PHPOrchestra\FrontBundle\Controller\NodeController::showAction',
             "nodeId" => $nodeId,
             "module_parameters" => $parameters
        );
    }

    /**
     * Return node info related to node matching slug and parent nodeId
     * Info returned are nodeId and NodeType
     *
     * @param string $slug
     * @param string $parentId
     *
     * @return array|bool
     */
    protected function getNode($slug, $parentId)
    {
        $criteria = array(
            'parentId' => (string) $parentId,
            'alias' => $slug
        );

        return $this->nodeRepository->findOneBy($criteria);
    }
}
