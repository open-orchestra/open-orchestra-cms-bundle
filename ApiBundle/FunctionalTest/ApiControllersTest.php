<?php

namespace PHPOrchestra\ApiBundle\FunctionalTest;

use Phake;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ApiControllersTest
 */
class ApiControllersTest extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;
    protected $currentSiteManager;
    protected $nodeRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->client = static::createClient();

        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'nicolas';
        $form['_password'] = 'nicolas';

        $crawler = $this->client->submit($form);

        $this->currentSiteManager = Phake::mock('PHPOrchestra\BaseBundle\Context\CurrentSiteIdInterface');
        Phake::when($this->currentSiteManager)->getCurrentSiteId()->thenReturn('1');
        Phake::when($this->currentSiteManager)->getCurrentSiteDefaultLanguage()->thenReturn('fr');
        $this->nodeRepository = static::$kernel->getContainer()->get('php_orchestra_model.repository.node');
    }

    /**
     * @param string $url
     *
     * @dataProvider provideApiUrl
     */
    public function testApi($url)
    {
        $crawler = $this->client->request('GET', $url);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $this->client->getResponse()->headers->get('content-type'));
    }

    /**
     * @return array
     */
    public function provideApiUrl()
    {
        return array(
            array('/api/node/root'),
            array('/api/node/root?language=en'),
            array('/api/node/transverse'),
            array('/api/node/fixture_full'),
            array('/api/node/fixture_full?language=en'),
            array('/api/content'),
            array('/api/content-type'),
            array('/api/site'),
            array('/api/theme'),
            array('/api/user'),
            array('/api/role'),
            array('/api/status'),
            array('/api/template/template_full'),
            array('/api/context/site/2/www.bphpOrchestra.fr'),
        );
    }

    /**
     * test reverse transform
     */
    public function testAreaReverseTransform()
    {
        $crawler = $this->client->request('GET', '/admin/');
        $crawler = $this->client->request('GET', '/api/context/site/1/front-phporchestra.dev');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->request('GET', '/api/node/root');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $json = json_decode($this->client->getResponse()->getContent(), true);
        $area = $json['areas'][0];
        $this->assertSame('main', $area['area_id']);
        $block = $area['blocks'][3];
        $update = $area['links']['_self_block'];

        // Remove ref of area in block 3
        $formData = json_encode(array('blocks' => array(
            array('node_id' => 'root', 'block_id' => 0),
            array('node_id' => 'root', 'block_id' => 1),
            array('node_id' => 'root', 'block_id' => 2),
        )));

        $crawler = $this->client->request('POST', $update, array(), array(), array(), $formData);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $nodeAfter = $this->nodeRepository->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion($block['node_id']);
        $this->assertSame(array(), $nodeAfter->getBlock(3)->getAreas());

        // Add ref of area in block 3
        $formData = json_encode(array('blocks' => array(
            array('node_id' => 'root', 'block_id' => 0),
            array('node_id' => 'root', 'block_id' => 1),
            array('node_id' => 'root', 'block_id' => 2),
            array('node_id' => 'root', 'block_id' => 3),
        )));

        $crawler = $this->client->request('POST', $update, array(), array(), array(), $formData);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
