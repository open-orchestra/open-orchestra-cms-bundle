<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\FrontBundle\Routing\PhpOrchestraUrlMatcher;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class CacheController
 */
class CacheController extends Controller
{
    /**
     * @Config\Route("/clear/cache/routing", name="php_orchestra_backoffice_clear_routing_cache")
     *
     * @return Response
     */
    public function clearRoutingCacheAction()
    {
        $cacheService = $this->container->get('php_orchestra_base.cache_manager');
        
        $cacheService->deleteKeys(PhpOrchestraUrlMatcher::PATH_PREFIX . '*');
        
        return $this->render('PHPOrchestraBackofficeBundle:Tools:clearRoutingCache.html.twig');
    }
}
