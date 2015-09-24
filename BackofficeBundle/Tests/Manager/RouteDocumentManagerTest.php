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

        $this->siteAliasFr = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');
        Phake::when($this->siteAliasFr)->getLanguage()->thenReturn('fr');
        Phake::when($this->siteAliasFr)->getDomain()->thenReturn($this->domainFr);
        Phake::when($this->siteAliasFr)->getScheme()->thenReturn(ReadSchemeableInterface::SCHEME_HTTPS);
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
            $this->nodeRepository
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

        $parent = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($parent)->getRoutePattern()->thenReturn('/bar');
        Phake::when($this->nodeRepository)
            ->findOnePublishedByNodeIdAndLanguageAndSiteIdInLastVersion(Phake::anyParameters())
            ->thenReturn($parent);

        $routeDocuments = $this->manager->createForNode($node);

        $this->assertCount(2, $routeDocuments);
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
            array('fr', 'nodeId', array(1, 3), '/foo', '/foo'),
            array('en', 'nodeId', array(0, 2), '/foo', '/foo'),
            array('fr', 'nodeId', array(1, 3), 'foo', 'foo'),
            array('en', 'nodeId', array(0, 2), 'foo', 'foo'),
            array('fr', 'nodeId', array(1, 3), '/foo', '/foo', NodeInterface::ROOT_NODE_ID),
            array('en', 'nodeId', array(0, 2), '/foo', '/foo', NodeInterface::ROOT_NODE_ID),
            array('fr', 'nodeId', array(1, 3), 'foo', 'foo', NodeInterface::ROOT_NODE_ID),
            array('en', 'nodeId', array(0, 2), 'foo', 'foo', NodeInterface::ROOT_NODE_ID),
            array('fr', 'nodeId', array(1, 3), '/foo', '/foo', '-'),
            array('en', 'nodeId', array(0, 2), '/foo', '/foo', '-'),
            array('fr', 'nodeId', array(1, 3), 'foo', 'foo', '-'),
            array('en', 'nodeId', array(0, 2), 'foo', 'foo', '-'),
            array('fr', 'nodeId', array(1, 3), '/foo', '/foo', 'parent'),
            array('en', 'nodeId', array(0, 2), '/foo', '/foo', 'parent'),
            array('fr', 'nodeId', array(1, 3), 'foo', '/bar/foo', 'parent'),
            array('en', 'nodeId', array(0, 2), 'foo', '/bar/foo', 'parent'),
        );
    }
}
