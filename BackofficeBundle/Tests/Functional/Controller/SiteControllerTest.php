<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Functional\Controller;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;

/**
 * Class SiteControllerTest
 *
 * @group backofficeTest
 */
class SiteControllerTest extends AbstractControllerTest
{
    /**
     * @var NodeRepositoryInterface
     */
    protected $nodeRepository;

    /**
     * @var SiteRepositoryInterface
     */
    protected $siteRepository;

    protected $siteId;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();

        $this->siteId = (string) microtime(true);
        $this->nodeRepository = static::$kernel->getContainer()->get('open_orchestra_model.repository.node');
        $this->siteRepository = static::$kernel->getContainer()->get('open_orchestra_model.repository.site');
    }

    /**
     * Test when you create a site and update it
     */
    public function testCreateSite()
    {
        $this->assertNodeCount(0, 'fr');
        $this->assertNodeCount(0, 'en');

        $this->createSite();

        $this->assertNodeCount(1, 'fr');
        $this->assertNodeCount(0, 'en');

        $crawler = $this->client->request('GET', '/admin/site/form/' . $this->siteId);
        $form = $crawler->selectButton('Save')->form();
        $form['site[aliases][0][language]'] = 'en';
        $this->client->submit($form);

        $this->assertNodeCount(1, 'fr');
        $this->assertNodeCount(1, 'en');
    }

    /**
     * Test create 2 site with the same siteId only one is save
     */
    public function testUniqueSiteId()
    {
        $this->assertSiteCount(0, $this->siteId);

        $this->createSite();

        $this->assertSiteCount(1, $this->siteId);

        $this->createSite();

        $this->assertSiteCount(1, $this->siteId);
    }

    /**
     * Create a site
     */
    protected function createSite()
    {
       $crawler =  $this->client->request('GET', '/admin/site/new');

        $form = $crawler->selectButton('Save')->form();
        $form['site[siteId]'] = $this->siteId;
        $form['site[name]'] = $this->siteId . 'domain';
        $form['site[aliases][0][domain]'] = $this->siteId . 'name';
        $form['site[aliases][0][language]'] = 'fr';
        $form['site[aliases][0][main]'] = true;

        $this->client->submit($form);
    }

    /**
     * @param int    $count
     * @param string $language
     */
    protected function assertNodeCount($count, $language)
    {
        $nodes = $this->nodeRepository->findByNodeAndLanguageAndSite(NodeInterface::TRANSVERSE_NODE_ID, $language, $this->siteId);

        $this->assertCount($count, $nodes);
    }

    /**
     * @param int    $count
     * @param string $siteId
     */
    protected function assertSiteCount($count, $siteId)
    {
        $sites = $this->siteRepository->findBy(array('siteId' => $siteId));

        $this->assertCount($count, $sites);
    }
}
