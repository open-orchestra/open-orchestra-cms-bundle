<?php

namespace PHPOrchestra\ApiBundle\FunctionalTest;

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
            array('/api/node/fixture_full'),
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
}
