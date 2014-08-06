<?php

namespace PHPOrchestra\FrontBundle\Routing;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The FrameworkBundle router is extended to inject documents service
 * in PhpOrchestraUrlMatcher
 */
class PhpOrchestraRouter extends Router
{
    protected $cacheService;
    protected $nodeRepository;
    
    /**
     * Extends parent constructor to get documents service
     * as $container is private in parent class
     *  
     * @param $container
     * @param $resource
     * @param $options
     * @param $context
     */
    public function __construct(
        ContainerInterface $container,
        $resource,
        array $options = array(),
        RequestContext $context = null
    ) {
        parent::__construct($container, $resource, $options, $context);
        
        $this->cacheService = $container->get('php_orchestra_cms.cache_manager');
        $this->nodeRepository = $container->get('php_orchestra_model.repository.node');
    }
    
    /**
     * Override parent getMatcher to inject documents service
     * in PhpOrchestraUrlMatcher
     */
    public function getMatcher()
    {
        if (null !== $this->matcher) {
            return $this->matcher;
        }
        
        return $this->matcher = new $this->options['matcher_class'](
            $this->getRouteCollection(),
            $this->context,
            $this->nodeRepository,
            $this->cacheService
        );
    }
    
    /**
     * Get the url generator
     */
    public function getGenerator()
    {
        if (null !== $this->generator) {
            return $this->generator;
        }
        
        return $this->generator = new $this->options['generator_class'](
            $this->getRouteCollection(),
            $this->context,
            $this->nodeRepository,
            $this->logger
        );
    }
}
