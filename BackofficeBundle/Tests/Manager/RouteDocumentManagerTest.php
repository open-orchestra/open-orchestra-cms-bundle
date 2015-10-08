<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BackofficeBundle\Manager\RouteDocumentManager;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\ReadSchemeableInterface;
use Phake;

/**
 * Test RouteDocumentManagerTest
 */
class RouteDocumentManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RouteDocumentManager
     */
    protected $manager;

    protected $routeDocumentRepository;
    protected $domainFr = 'domain.fr';
    protected $domainEn = 'domain.en';
    protected $routeDocumentClass;
    protected $siteRepository;
    protected $nodeRepository;
    protected $siteAliasEn;
    protected $siteAliasFr;
    protected $site;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->routeDocumentClass = 'OpenOrchestra\ModelBundle\Document\RouteDocument';

        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->routeDocumentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\RouteDocumentRepositoryInterface');

        $this->siteAliasFr = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');
        Phake::when($this->siteAliasFr)->getLanguage()->thenReturn('fr');
        Phake::when($this->siteAliasFr)->getDomain()->thenReturn($this->domainFr);
        Phake::when($this->siteAliasFr)->getScheme()->thenReturn(ReadSchemeableInterface::SCHEME_HTTPS);
        Phake::when($this->siteAliasFr)->getPrefix()->thenReturn('prefixFr');
        $this->siteAliasEn = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');
        Phake::when($this->siteAliasEn)->getLanguage()->thenReturn('en');
        Phake::when($this->siteAliasEn)->getDomain()->thenReturn($this->domainEn);
        Phake::when($this->siteAliasEn)->getScheme()->thenReturn(ReadSchemeableInterface::SCHEME_HTTPS);
        $siteAliases = new ArrayCollection(array(
            $this->siteAliasEn,
            $this->siteAliasFr,
            $this->siteAliasEn,
            $this->siteAliasFr,
        ));
        $this->site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($this->site)->getAliases()->thenReturn($siteAliases);
        Phake::when($this->site)->getSiteId()->thenReturn('siteId');
        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        Phake::when($this->siteRepository)->findOneBySiteId(Phake::anyParameters())->thenReturn($this->site);

        $this->manager = new RouteDocumentManager(
            $this->routeDocumentClass,
            $this->siteRepository,
            $this->nodeRepository,
            $this->routeDocumentRepository
        );
    }

    /**
     * @dataProvider provideNodeData
     */
    public function testCreateForNode($language, $id, array $aliasIds, $pattern, $exceptedPattern, $parentId = null)
    {
        $nodeId = 'nodeId';
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getLanguage()->thenReturn($language);
        Phake::when($node)->getId()->thenReturn($id);
        Phake::when($node)->getNodeId()->thenReturn($nodeId);
        Phake::when($node)->getRoutePattern()->thenReturn($pattern);
        Phake::when($node)->getParentId()->thenReturn($parentId);

        Phake::when($this->nodeRepository)
            ->findPublishedInLastVersion($nodeId, $language, 'siteId')
            ->thenReturn($node);

        $parent = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($parent)->getRoutePattern()->thenReturn('/bar');
        Phake::when($this->nodeRepository)
            ->findOnePublishedByNodeIdAndLanguageAndSiteIdInLastVersion(Phake::anyParameters())
            ->thenReturn($parent);

        $routeDocuments = $this->manager->createForNode($node);

        $this->assertCount(2, $routeDocuments);
        Phake::verify($this->nodeRepository)->findPublishedInLastVersion($nodeId, $language, 'siteId');
        foreach ($routeDocuments as $key => $route) {
            $this->assertSame($aliasIds[$key] . '_' . $id, $route->getName());
            $this->assertSame($this->{'domain'. ucfirst($language)}, $route->getHost());
            $this->assertSame(ReadSchemeableInterface::SCHEME_HTTPS, $route->getSchemes());
            $this->assertSame(array(
                '_locale' => $language,
                'nodeId' => $nodeId,
                'siteId' => 'siteId',
                'aliasId' => $aliasIds[$key],
            ), $route->getDefaults());
            $this->assertSame($nodeId, $route->getNodeId());
            $this->assertSame('siteId', $route->getSiteId());
            $this->assertSame($language, $route->getLanguage());
            $this->assertSame($exceptedPattern, $route->getPattern());
        }
    }

    /**
     * @return array
     */
    public function provideNodeData()
    {
        return array(
            array('fr', 'nodeId', array(1, 3), '/foo', '/prefixFr/foo'),
            array('en', 'nodeId', array(0, 2), '/foo', '/foo'),
            array('fr', 'nodeId', array(1, 3), 'foo', '/prefixFr/foo'),
            array('en', 'nodeId', array(0, 2), 'foo', 'foo'),
            array('fr', 'nodeId', array(1, 3), '/foo', '/prefixFr/foo', NodeInterface::ROOT_NODE_ID),
            array('en', 'nodeId', array(0, 2), '/foo', '/foo', NodeInterface::ROOT_NODE_ID),
            array('fr', 'nodeId', array(1, 3), 'foo', '/prefixFr/foo', NodeInterface::ROOT_NODE_ID),
            array('en', 'nodeId', array(0, 2), 'foo', 'foo', NodeInterface::ROOT_NODE_ID),
            array('fr', 'nodeId', array(1, 3), '/foo', '/prefixFr/foo', '-'),
            array('en', 'nodeId', array(0, 2), '/foo', '/foo', '-'),
            array('fr', 'nodeId', array(1, 3), 'foo', '/prefixFr/foo', '-'),
            array('en', 'nodeId', array(0, 2), 'foo', 'foo', '-'),
            array('fr', 'nodeId', array(1, 3), '/foo', '/prefixFr/foo', 'parent'),
            array('en', 'nodeId', array(0, 2), '/foo', '/foo', 'parent'),
            array('fr', 'nodeId', array(1, 3), 'foo', '/prefixFr/bar/foo', 'parent'),
            array('en', 'nodeId', array(0, 2), 'foo', '/bar/foo', 'parent'),
        );
    }

    /**
     * @param string $nodeId
     * @param string $siteId
     * @param string $language
     *
     * @dataProvider provideClearNodeData
     */
    public function testClearForNode($nodeId, $siteId, $language)
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getLanguage()->thenReturn($language);
        Phake::when($node)->getSiteId()->thenReturn($siteId);
        Phake::when($node)->getNodeId()->thenReturn($nodeId);

        $routeDocuments = new ArrayCollection();
        Phake::when($this->routeDocumentRepository)->findByNodeIdSiteIdAndLanguage(Phake::anyParameters())->thenReturn($routeDocuments);

        $routes = $this->manager->clearForNode($node);

        $this->assertSame($routeDocuments, $routes);
        Phake::verify($this->routeDocumentRepository)->findByNodeIdSiteIdAndLanguage($nodeId, $siteId, $language);
    }

    /**
     * @return array
     */
    public function provideClearNodeData()
    {
        return array(
            array('root', '2', 'fr'),
            array('foo', 'bar', 'en'),
        );
    }

    /**
     * @param string $locale
     * @param string $id
     * @param array  $aliasIds
     * @param bool   $permanent
     * @param string $pattern
     *
     * @dataProvider provideNodeRedirectionData
     */
    public function testcreateOrUpdateForRedirectionToNode($locale, $id, $aliasIds, $permanent, $pattern)
    {
        $mongoNodeId = 'mongoNodeId';
        $nodeId = 'nodeId';
        $siteId = 'siteId';
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getId()->thenReturn($mongoNodeId);
        Phake::when($node)->getNodeId()->thenReturn($nodeId);
        Phake::when($node)->getSiteId()->thenReturn($siteId);

        $redirection = Phake::mock('OpenOrchestra\ModelInterface\Model\RedirectionInterface');
        Phake::when($redirection)->getId()->thenReturn($id);
        Phake::when($redirection)->getNodeId()->thenReturn($nodeId);
        Phake::when($redirection)->isPermanent()->thenReturn($permanent);
        Phake::when($redirection)->getSiteId()->thenReturn($siteId);
        Phake::when($redirection)->getRoutePattern()->thenReturn($pattern);
        Phake::when($redirection)->getLocale()->thenReturn($locale);

        Phake::when($this->nodeRepository)->findOnePublishedByNodeIdAndLanguageAndSiteIdInLastVersion(Phake::anyParameters())->thenReturn($node);

        $routeDocuments = $this->manager->createOrUpdateForRedirection($redirection);

        $this->assertCount(2, $routeDocuments);
        foreach ($routeDocuments as $key => $route) {
            $this->assertSame($aliasIds[$key] . '_' . $id, $route->getName());
            $this->assertSame($this->{'domain'. ucfirst($locale)}, $route->getHost());
            $this->assertSame(array(
                '_controller' => 'FrameworkBundle:Redirect:redirect',
                'route' => $aliasIds[$key] . '_' . $mongoNodeId,
                'permanent' => $permanent,
            ), $route->getDefaults());
        }
        Phake::verify($this->siteRepository)->findOneBySiteId($siteId);
        Phake::verify($this->nodeRepository)->findOnePublishedByNodeIdAndLanguageAndSiteIdInLastVersion($nodeId, $locale, $siteId);
    }

    /**
     * @return array
     */
    public function provideNodeRedirectionData()
    {
        return array(
            array('fr', 'nodeId', array(1, 3), true, '/foo'),
            array('en', 'nodeId', array(0, 2), false, '/bar'),
        );
    }

    /**
     * @param string $locale
     * @param string $id
     * @param array  $aliasIds
     * @param bool   $permanent
     * @param string $pattern
     * @param string $url
     *
     * @dataProvider provideUrlRedirectionData
     */
    public function testCreateRedirectionToUrl($locale, $id, $aliasIds, $permanent, $pattern, $url)
    {
        $siteId = 'siteId';

        $redirection = Phake::mock('OpenOrchestra\ModelInterface\Model\RedirectionInterface');
        Phake::when($redirection)->getId()->thenReturn($id);
        Phake::when($redirection)->isPermanent()->thenReturn($permanent);
        Phake::when($redirection)->getSiteId()->thenReturn($siteId);
        Phake::when($redirection)->getRoutePattern()->thenReturn($pattern);
        Phake::when($redirection)->getUrl()->thenReturn($url);
        Phake::when($redirection)->getLocale()->thenReturn($locale);

        $routeDocuments = $this->manager->createOrUpdateForRedirection($redirection);

        $this->assertCount(2, $routeDocuments);
        foreach ($routeDocuments as $key => $route) {
            $this->assertSame($aliasIds[$key] . '_' . $id, $route->getName());
            $this->assertSame($this->{'domain'. ucfirst($locale)}, $route->getHost());
            $this->assertSame(array(
                '_controller' => 'FrameworkBundle:Redirect:urlRedirect',
                'path' => $url,
                'permanent' => $permanent,
            ), $route->getDefaults());
        }
        Phake::verify($this->siteRepository)->findOneBySiteId($siteId);
        Phake::verify($this->nodeRepository, Phake::never())->findOnePublishedByNodeIdAndLanguageAndSiteIdInLastVersion(Phake::anyParameters());

    }

    /**
     * @return array
     */
    public function provideUrlRedirectionData()
    {
        return array(
            array('fr', 'nodeId', array(1, 3), true, '/foo', 'http://foo.bar'),
            array('en', 'nodeId', array(0, 2), false, '/bar', 'http://bar.baz'),
        );
    }
}
