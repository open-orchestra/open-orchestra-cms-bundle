<?php

namespace PHPOrchestra\BackofficeBundle\FunctionalTest\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractControllerTest
 */
abstract class AbstractControllerTest extends WebTestCase
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
        $form['_username'] = 'nicolas';
        $form['_password'] = 'nicolas';

        $crawler = $this->client->submit($form);
    }

    /**
     * @param Response $response
     */
    protected function assertForm(Response $response)
    {
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertRegExp('/form/', $response->getContent());
        $this->assertNotRegExp('/<html/', $response->getContent());
        $this->assertNotRegExp('/_username/', $response->getContent());
    }
}
