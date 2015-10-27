<?php

namespace OpenOrchestra\ApiBundle\Tests\Functional\Controller;

use Phake;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
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
    protected $username = 'admin';
    protected $password = 'admin';

    /**
     * @var NodeRepositoryInterface
     */
    protected $nodeRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->client = static::createClient();

        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Log in')->form();
        $form['_username'] = $this->username;
        $form['_password'] = $this->password;

        $this->client->submit($form);

        $this->currentSiteManager = Phake::mock('OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface');
        Phake::when($this->currentSiteManager)->getCurrentSiteId()->thenReturn('2');
        Phake::when($this->currentSiteManager)->getCurrentSiteDefaultLanguage()->thenReturn('fr');

        $this->nodeRepository = static::$kernel->getContainer()->get('open_orchestra_model.repository.node');
    }
}
