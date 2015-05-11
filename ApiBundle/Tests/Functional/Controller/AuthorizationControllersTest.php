<?php

namespace OpenOrchestra\ApiBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Class AuthorizationControllersTest
 */
class AuthorizationControllersTest extends WebTestCase
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
    }

    /**
     * Test token creation and usage
     */
    public function testTokenCreationAndUsage()
    {
        $this->client->request('GET', '/api/node/root');
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $this->client->getResponse()->headers->get('content-type'));
        $this->assertContains('client.access_denied', $this->client->getResponse()->getContent());

        $this->client->request('GET', '/api/node/root?access_token=access_token');
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $this->client->getResponse()->headers->get('content-type'));
        $this->assertContains('token.blocked', $this->client->getResponse()->getContent());

        $this->client->request('GET', '/oauth/access_token?grant_type=password&username=admin&password=admin', array(), array(), array('PHP_AUTH_USER' => 'test_key', 'PHP_AUTH_PW' => 'test_secret'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $this->client->getResponse()->headers->get('content-type'));
        $tokenReponse = json_decode($this->client->getResponse()->getContent(), true);
        $accessToken = $tokenReponse['access_token'];

        $this->client->request('GET', '/api/node/root?access_token=' . $accessToken);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $this->client->getResponse()->headers->get('content-type'));
    }
}
