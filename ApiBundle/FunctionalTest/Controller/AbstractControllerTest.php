<?php

namespace PHPOrchestra\ApiBundle\FunctionalTest\Controller;

use Phake;
use PHPOrchestra\ModelBundle\Repository\NodeRepository;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AbstractControllerTest
 */
abstract class AbstractControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    protected $currentSiteManager;

    /**
     * @var NodeRepository
     */
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
}
