<?php

namespace OpenOrchestra\Backoffice\Manager;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\ModelInterface\Model\RedirectionInterface;
use OpenOrchestra\ModelInterface\Model\RouteDocumentInterface;
use OpenOrchestra\ModelInterface\Model\SchemeableInterface;
use OpenOrchestra\ModelInterface\Model\SiteAliasInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\RedirectionRepositoryInterface;
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
    protected $redirectionRepository;

    /**
     * @param string                           $routeDocumentClass
     * @param SiteRepositoryInterface          $siteRepository
     * @param NodeRepositoryInterface          $nodeRepository
     * @param RedirectionRepositoryInterface   $redirectionRepository
     * @param RouteDocumentRepositoryInterface $routeDocumentRepository
     */
    public function __construct(
        $routeDocumentClass,
        SiteRepositoryInterface $siteRepository,
        NodeRepositoryInterface $nodeRepository,
        RedirectionRepositoryInterface $redirectionRepository,
        RouteDocumentRepositoryInterface $routeDocumentRepository
    )
    {
        $this->routeDocumentRepository = $routeDocumentRepository;
        $this->routeDocumentClass = $routeDocumentClass;
        $this->siteRepository = $siteRepository;
        $this->nodeRepository = $nodeRepository;
        $this->redirectionRepository = $redirectionRepository;
    }

    /**
     * @param SiteInterface $site
     *
     * @return array
     */
    public function createForSite(SiteInterface $site)
    {
        $listRedirection = $this->redirectionRepository->findBySiteId($site->getSiteId());
        $siteLanguages = $site->getLanguages();
        $routes = array();

        foreach ($siteLanguages as $language) {
            $nodes = $this->nodeRepository->findPublishedByLanguageAndSiteId($site->getSiteId(), $language);

            $routePattern = $this->nodeRepository->findAllRoutePattern($language, $site->getSiteId());
            $routePattern = $this->computeRoutePattern($routePattern);
            foreach ($nodes as $node) {
                $routes = array_merge($this->generateRoutesForNode($node, $site, $routePattern), $routes);
            }
        }

        foreach ($listRedirection as $redirection) {
            $routes = array_merge($this->generateRoutesForRedirection($redirection, $site), $routes);
        }

        return $routes;
    }

    /**
     * @param SiteInterface $site
     */
    public function clearForSite(SiteInterface $site)
    {
        $this->routeDocumentRepository->removeBySiteId($site->getSiteId());
    }

    /**
     * @param NodeInterface $node
     *
     * @return array
     */
    public function createForNode(NodeInterface $node)
    {
        $site = $this->siteRepository->findOneBySiteId($node->getSiteId());
        $nodes = $this->nodeRepository->findPublishedByPathAndLanguage($node->getPath(), $site->getSiteId(), $node->getLanguage());

        $routePattern = $this->nodeRepository->findAllRoutePattern($node->getLanguage(), $site->getSiteId());
        $routePattern = $this->computeRoutePattern($routePattern);
        $routes = array();

        foreach ($nodes as $node) {
            $routes = array_merge($this->generateRoutesForNode($node, $site, $routePattern), $routes);
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
        $site = $this->siteRepository->findOneBySiteId($redirection->getSiteId());

        return $this->generateRoutesForRedirection($redirection, $site);
    }

    /**
     * @param RedirectionInterface $redirection
     */
    public function deleteForRedirection(RedirectionInterface $redirection)
    {
        $this->routeDocumentRepository->removeByRedirectionId($redirection->getId());
    }

    /**
     * @param string|null $parentId
     * @param string|null $suffix
     * @param string      $language
     * @param string      $siteId
     * @param array       $routePattern
     *
     * @return string|null
     */
    protected function completeRoutePattern($parentId = null, $suffix = null, $language, $siteId, array $routePattern)
    {
        if (is_null($parentId) || NodeInterface::ROOT_PARENT_ID == $parentId || '' == $parentId || 0 === strpos($suffix, '/')) {
            return $suffix;
        }

        if (isset($routePattern[$parentId])) {
            $parent = $routePattern[$parentId];

            return $this->suppressDoubleSlashes($this->completeRoutePattern($parent['parentId'], $parent['routePattern'] . '/' . $suffix, $language, $siteId, $routePattern));
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
        $nodeIds = $this->nodeRepository->findNodeIdByIncludedPathSiteIdAndLanguage(
            $node->getPath(),
            $node->getSiteId(),
            $node->getLanguage()
        );

        $this->routeDocumentRepository->removeByNodeIdsSiteIdAndLanguage($nodeIds, $node->getSiteId(), $node->getLanguage());
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

        $node = $this->nodeRepository->findOnePublished(
            $redirection->getNodeId(),
            $redirection->getLocale(),
            $redirection->getSiteId()
        );

        return $node;
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
     * @param array $nodes
     *
     * @return array
     */
    protected function computeRoutePattern(array $nodes)
    {
        $routePattern = array();
        foreach ($nodes as $node) {
            if (isset($node['nodeId']) && isset($node['routePattern']) && isset($node['parentId'])) {
                $routePattern[$node['nodeId']] = $node;
            }
        }

        return $routePattern;
    }

    /**
     * @param NodeInterface     $node
     * @param ReadSiteInterface $site
     * @param array             $routePattern
     *
     * @return array
     */
    protected function generateRoutesForNode(NodeInterface $node, ReadSiteInterface $site, array $routePattern)
    {
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
                $pattern = $this->completeRoutePattern($node->getParentId(), $node->getRoutePattern(), $node->getLanguage(), $site->getSiteId(), $routePattern);
                if ($alias->getPrefix()) {
                    $pattern = $this->suppressDoubleSlashes('/' . $alias->getPrefix() . '/' . $pattern);
                }
                $route->setPattern($pattern);
                $routes[] = $route;
            }
        }

        return $routes;
    }

    /**
     * @param RedirectionInterface $redirection
     * @param ReadSiteInterface    $site
     *
     * @return array
     */
    protected function generateRoutesForRedirection(RedirectionInterface $redirection, ReadSiteInterface $site)
    {
        $routes = array();

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
                $route = new $this->routeDocumentClass();
                $route->setName($key . '_' . $redirection->getId());
                $route->setHost($alias->getDomain());
                $route->setSiteId($site->getSiteId());
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
}
