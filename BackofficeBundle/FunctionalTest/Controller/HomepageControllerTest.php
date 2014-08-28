<?php

namespace PHPOrchestra\BackofficeBundle\FunctionalTest\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class HomepageControllerTest
 */
class HomepageControllerTest extends WebTestCase
{
    /**
     * Test fixture_home
     */
    public function testHomepageWithTree()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/');

        $this->assertEquals(1, $crawler->filter('html:contains("Editorial")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Administration")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Fixture full sample")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Content")')->count());
    }
}
