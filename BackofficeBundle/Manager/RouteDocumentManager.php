<?php

namespace OpenOrchestra\BackofficeBundle\Manager;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\ModelInterface\Model\RedirectionInterface;
use OpenOrchestra\ModelInterface\Model\RouteDocumentInterface;
use OpenOrchestra\ModelInterface\Model\SchemeableInterface;
use OpenOrchestra\ModelInterface\Model\SiteAliasInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
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
     * @param SiteInterface $site
     *
     * @return array
     */
    public function createForSite(SiteInterface $site)
    {
        $nodes = $this->nodeRepository->findLastVersionByType($site->getSiteId());

        $routes = array();
        foreach ($nodes as $node) {
            $routes = array_merge($this->generateRoutesForNode($node, $site), $routes);
        }

        return $routes;
    }

    /**
     * @param SiteInterface $site
     *
     * @return array
     */
    public function clearForSite(SiteInterface $site)
    {
        return $this->routeDocumentRepository->findBySite($site->getSiteId());
    }

    /**
     * @param NodeInterface $givenNode
     *
     * @return array
     */
    public function createForNode(NodeInterface $node)
    {
        $site = $this->siteRepository->findOneBySiteId($node->getSiteId());
        $nodes = $this->getTreeNode($node, $site);

        $routes = array();

        foreach ($nodes as $node) {
            $routes = array_merge($this->generateRoutesForNode($node, $site), $routes);
        }

        return $routes;
    }

    /**
     * @param RedirectionInterface $redirection
     *
     * @return array
     */
    public function createOrUpdateForRedirection(RedirectionInterface $redirection)
    {
        $routes = array();
        $site = $this->siteRepository->findOneBySiteId($redirection->getSiteId());
        $node = $this->getNodeForRedirection($redirection);
        $controller = 'FrameworkBundle:Redirect:urlRedirect';
        $paramKey = 'path';
        if ($node instanceof NodeInterface) {
            $controller = 'FrameworkBundle:Redirect:redirect';
            $paramKey = 'route';
        }

        /** @var SiteAliasInterface $alias */
        foreach ($site->getAliases() as $key => $alias) {
            if ($alias->getLanguage() == $redirection->getLocale()) {
                /** @var RouteDocumentInterface $route */
                $route = $this->getOrCreateRouteDocument($redirection, $key);
                $route->setHost($alias->getDomain());
                if ($node instanceof NodeInterface) {
                    $paramValue = $key . '_' . $node->getId();
                } else {
                    $paramValue = $redirection->getUrl();
                }
                $route->setDefaults(array(
                    '_controller' => $controller,
                    $paramKey => $paramValue,
                    'permanent' => $redirection->isPermanent()
                ));
                $route->setPattern($redirection->getRoutePattern());
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

        $parent = $this->nodeRepository->findPublishedInLastVersion($parentId, $language, $siteId);

        if ($parent instanceof NodeInterface) {
            return $this->suppressDoubleSlashes($this->completeRoutePattern($parent->getParentId(), $parent->getRoutePattern() . '/' . $suffix, $language, $siteId));
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
        $nodes = $this->getTreeNode($node);
        $routes = array();

        foreach ($nodes as $node) {
            $routes = array_merge($this->routeDocumentRepository->findByNodeIdSiteIdAndLanguage($node->getNodeId(), $node->getSiteId(), $node->getLanguage()), $routes);
        }

        return $routes;
    }

    protected function getTreeNode(NodeInterface $node, $site = null)
    {
        $nodes = array($node);
        $site = (null === $site) ? $this->siteRepository->findOneBySiteId($node->getSiteId()) : $site;
        $children = $this->nodeRepository->findByParent($node->getNodeId(), $site->getSiteId());

        foreach ($children as $child) {
            $nodes = array_merge($this->getTreeNode($child, $site), $nodes);
        }

        return $nodes;
    }


    /**
     * @param RedirectionInterface $redirection
     *
     * @return NodeInterface|null
     */
    protected function getNodeForRedirection(RedirectionInterface $redirection)
    {
        if (is_null($redirection->getNodeId())) {
            return null;
        }

        $node = $this->nodeRepository->findPublishedInLastVersion(
            $redirection->getNodeId(),
            $redirection->getLocale(),
            $redirection->getSiteId()
        );

        return $node;
    }

    /**
     * @param RedirectionInterface $redirection
     * @param int                  $key
     *
     * @return RouteDocumentInterface
     */
    protected function getOrCreateRouteDocument(RedirectionInterface $redirection, $key)
    {
        $routeDocumentClass = $this->routeDocumentClass;
        $routeName = $key . '_' . $redirection->getId();

        if (!($route = $this->routeDocumentRepository->findOneByName($routeName))) {
            $route = new $routeDocumentClass();
            $route->setName($routeName);
        }

        return $route;
    }

    /**
     * @param string $route
     *
     * @return string
     */
    protected function suppressDoubleSlashes($route)
    {
        return str_replace('//', '/', $route);
    }

    /**
     * @param NodeInterface     $givenNode
     * @param ReadSiteInterface $site
     *
     * @return array
     */
    protected function generateRoutesForNode(NodeInterface $givenNode, ReadSiteInterface $site)
    {
        $node = $this->nodeRepository->findPublishedInLastVersion($givenNode->getNodeId(), $givenNode->getLanguage(), $site->getSiteId());
        $routes = array();

        if (!$node instanceof NodeInterface) {
            return $routes;
        }

        $routeDocumentClass = $this->routeDocumentClass;

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
                if ($alias->getPrefix()) {
                    $pattern = $this->suppressDoubleSlashes('/' . $alias->getPrefix() . '/' . $pattern);
                }
                $route->setPattern($pattern);
                $routes[] = $route;
            }
        }

        return $routes;
    }
}
