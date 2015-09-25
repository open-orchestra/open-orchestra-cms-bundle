<?php

namespace OpenOrchestra\BackofficeBundle\Manager;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\RouteDocumentInterface;
use OpenOrchestra\ModelInterface\Model\SchemeableInterface;
use OpenOrchestra\ModelInterface\Model\SiteAliasInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\RouteDocumentRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;

/**
 * Class RouteDocumentManager
 */
class RouteDocumentManager
{
    protected $routeDocumentRepository;
    protected $routeDocumentClass;
    protected $siteRepository;
    protected $nodeRepository;

    /**
     * @param string                           $routeDocumentClass
     * @param SiteRepositoryInterface          $siteRepository
     * @param NodeRepositoryInterface          $nodeRepository
     * @param RouteDocumentRepositoryInterface $routeDocumentRepository
     */
    public function __construct(
        $routeDocumentClass,
        SiteRepositoryInterface $siteRepository,
        NodeRepositoryInterface $nodeRepository,
        RouteDocumentRepositoryInterface $routeDocumentRepository
    )
    {
        $this->routeDocumentRepository = $routeDocumentRepository;
        $this->routeDocumentClass = $routeDocumentClass;
        $this->siteRepository = $siteRepository;
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * @param NodeInterface $node
     *
     * @return array
     */
    public function createForNode(NodeInterface $node)
    {
        $routeDocumentClass = $this->routeDocumentClass;
        $routes = array();
        $site = $this->siteRepository->findOneBySiteId($node->getSiteId());

        /** @var SiteAliasInterface $alias */
        foreach ($site->getAliases() as $key => $alias) {
            if ($alias->getLanguage() == $node->getLanguage()) {
                /** @var RouteDocumentInterface $route */
                $route = new $routeDocumentClass();
                $route->setName($key . '_' . $node->getId());
                $route->setHost($alias->getDomain());
                $scheme = $node->getScheme();
                if (is_null($scheme) || SchemeableInterface::SCHEME_DEFAULT == $scheme) {
                    $scheme = $alias->getScheme();
                }
                $route->setSchemes($scheme);
                $route->setLanguage($node->getLanguage());
                $route->setNodeId($node->getNodeId());
                $route->setSiteId($site->getSiteId());
                $route->setAliasId($key);
                $pattern = $this->completeRoutePattern($node->getParentId(), $node->getRoutePattern(), $node->getLanguage(), $site->getSiteId());
                $route->setPattern($pattern);
                $routes[] = $route;
            }
        }

        return $routes;
    }

    /**
     * @param string|null $parentId
     * @param string|null $suffix
     * @param string      $language
     * @param string      $siteId
     *
     * @return string|null
     */
    protected function completeRoutePattern($parentId = null, $suffix = null, $language, $siteId)
    {
        if (is_null($parentId) || '-' == $parentId || '' == $parentId || NodeInterface::ROOT_NODE_ID == $parentId || 0 === strpos($suffix, '/')) {
            return $suffix;
        }

        $parent = $this->nodeRepository->findOnePublishedByNodeIdAndLanguageAndSiteIdInLastVersion($parentId, $language, $siteId);

        if ($parent instanceof NodeInterface) {
            return str_replace('//', '/', $this->completeRoutePattern($parent->getParentId(), $parent->getRoutePattern() . '/' . $suffix, $language, $siteId));
        }

        return $suffix;
    }

    /**
     * @param NodeInterface $node
     *
     * @return Collection
     */
    public function clearForNode(NodeInterface $node)
    {
        return $this->routeDocumentRepository->findByNodeIdSiteIdAndLanguage($node->getNodeId(), $node->getSiteId(), $node->getLanguage());
    }
}
