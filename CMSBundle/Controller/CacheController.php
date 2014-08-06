<?php
/**
 * This file is part of the PHPOrchestra\CMSBundle.
 *
 * @author Noël Gilain <noel.gilain@businessdecision.com>
 */

namespace PHPOrchestra\CMSBundle\Controller;

use PHPOrchestra\FrontBundle\Routing\PhpOrchestraUrlMatcher;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CacheController extends Controller
{
    /**
     * Delete all routing cache 
     */
    public function clearRoutingCacheAction()
    {
        $cacheService = $this->container->get('php_orchestra_base.cache_manager');
        
        $cacheService->deleteKeys(PhpOrchestraUrlMatcher::PATH_PREFIX . '*');
        
        return $this->render('PHPOrchestraCMSBundle:BackOffice/Tools:clearRoutingCache.html.twig');
    }
}
