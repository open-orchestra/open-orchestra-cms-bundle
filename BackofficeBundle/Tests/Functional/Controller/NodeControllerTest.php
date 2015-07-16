<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Functional\Controller;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * Class NodeControllerTest
 */
class NodeControllerTest extends AbstractControllerTest
{
    /**
     * @var NodeRepositoryInterface
     */
    protected $nodeRepository;
    protected $language = 'en';
    protected $siteId = '2';


    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();

        $this->nodeRepository = static::$kernel->getContainer()->get('open_orchestra_model.repository.node');
    }

    /**
     * Test some of the node forms
     */
    public function testNodeForms()
    {
        $nodeRoot = $this->nodeRepository->findOneByNodeIdAndLanguageAndSiteIdInLastVersion(NodeInterface::ROOT_NODE_ID, $this->language, $this->siteId);
        $nodeTransverse = $this->nodeRepository->findOneByNodeIdAndLanguageAndSiteIdInLastVersion(NodeInterface::TRANSVERSE_NODE_ID, $this->language, $this->siteId);
        $nodeFixtureCommunity = $this->nodeRepository->findOneByNodeIdAndLanguageAndSiteIdInLastVersion('fixture_page_community', $this->language, $this->siteId);

        $url = '/admin/node/form/' . $nodeRoot->getId();
        $this->client->request('GET', $url);
        $this->assertForm($this->client->getResponse());

        $url = '/admin/node/new/' . $nodeRoot->getNodeId();
        $this->client->request('GET', $url);
        $this->assertForm($this->client->getResponse());

        $url = '/admin/node/form/' . $nodeTransverse->getId();
        $this->client->request('GET', $url);
        $this->assertForm($this->client->getResponse());

        $url = '/admin/node/form/' . $nodeFixtureCommunity->getId();
        $this->client->request('GET', $url);
        $this->assertForm($this->client->getResponse());

        $url = '/admin/node/new/' . $nodeFixtureCommunity->getNodeId();
        $this->client->request('GET', $url);
        $this->assertForm($this->client->getResponse());

        $url = '/admin/area/form/' . $nodeFixtureCommunity->getId() . '/mainContentArea1';
        $this->client->request('GET', $url);
        $this->assertForm($this->client->getResponse());

        $url = '/admin/block/form/' . $nodeFixtureCommunity->getId();
        $this->client->request('GET', $url);
        $this->assertForm($this->client->getResponse());
    }

    /**
     * Test assert Node transverse always editable
     */
    public function testNodeTransverseEditable()
    {
        $nodeTransverse = $this->nodeRepository->findOneByNodeIdAndLanguageAndSiteIdInLastVersion(NodeInterface::TRANSVERSE_NODE_ID, $this->language, $this->siteId);

        $url = '/admin/node/form/' . $nodeTransverse->getId();
        $crawler = $this->client->request('GET', $url);
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form);

        $this->assertForm($this->client->getResponse());
    }

    /**
     * test new Node
     */
    public function testNewNodePageHome()
    {
        $crawler = $this->client->request('GET', '/admin/');
        $nbLink = $crawler->filter('a')->count();

        $crawler = $this->client->request('GET', '/admin/node/new/fixture_page_community');

        $formUser = $crawler->selectButton('node_submit')->form();

        $nodeName = 'fixturetest' . time();
        $formUser['node[name]'] = 'fixturetest' . time();
        $formUser['node[nodeSource]'] = 'root';
        $formUser['node[routePattern]'] = '/page-test' .time();

        $this->client->submit($formUser);
        $crawler = $this->client->request('GET', '/admin/');

        $this->assertEquals($nbLink + 2, $crawler->filter('a')->count());

        $this->client->request('GET', '/api/node/' . $nodeName);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $this->client->getResponse()->headers->get('content-type'));

        $this->client->request('DELETE', '/api/node/' . $nodeName . '/delete');
    }
}
