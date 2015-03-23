<?php

namespace OpenOrchestra\BackofficeBundle\FunctionalTest\Controller;

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

        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'benjamin';
        $form['_password'] = 'benjamin';

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
