<?php

namespace PHPOrchestra\BackofficeBundle\FunctionalTest\Controller;

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
            array('/admin/node/form/root'),
            array('/admin/node/form/fixture_full'),
            array('/admin/area/form/fixture_full/left_menu'),
            array('/admin/block/form/fixture_full/1'),
            array('/admin/block/form/fixture_full/3'),
            array('/admin/block/form/fixture_full/7'),
            array('/admin/site/form/1'),
            array('/admin/status/new'),
            array('/admin/theme/new'),
            array('/admin/template/form/template_full'),
            array('/admin/template/area/form/template_full/left_menu'),
            array('/admin/content/form/1'),
            array('/admin/content-type/form/car'),
            array('/admin/content-type/new'),
            array('/admin/media/new/54354a8b0f870926168b45aa'),
            array('/admin/node/new/fixture_page_what_is_orchestra'),
        );
    }
}
