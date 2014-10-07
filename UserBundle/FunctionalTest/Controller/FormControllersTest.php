<?php

namespace PHPOrchestra\UserBundle\FunctionalTest\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class FormControllersTest
 */
class FormControllersTest extends WebTestCase
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
        $this->client->setServerParameters(
            array(
                'PHP_AUTH_USER' => 'nicolas',
                'PHP_AUTH_PW'   => 'nicolas',
            )
        );
        $this->client->followRedirects();
    }

    /**
     * @param string $url
     *
     * @dataProvider provideApiUrl
     */
    public function testForm($url)
    {
        $crawler = $this->client->request('GET', $url);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return array
     */
    public function provideApiUrl()
    {
        return array(
            array('/admin/user/new'),
            array('/admin/role/new'),
        );
    }
}
