<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AuthorizationControllerTest
 */
class AuthorizationControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->client = static::createClient();
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Log in')->form();
        $form['_username'] = 'userLog';
        $form['_password'] = 'userLog';

        $this->client->submit($form);
    }

    /**
     * Test on form not available
     */
    public function testFormNotAvailable()
    {
        $this->client->request('GET', '/admin/theme/new');

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }
}
