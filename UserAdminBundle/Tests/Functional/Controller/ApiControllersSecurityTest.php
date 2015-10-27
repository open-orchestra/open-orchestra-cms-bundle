<?php

namespace OpenOrchestra\UserAdminBundle\Tests\Functional\Controller;

use OpenOrchestra\ApiBundle\Tests\Functional\Controller\AbstractControllerTest;

/**
 * Class ApiControllersSecurityTest
 */
class ApiControllersSecurityTest extends AbstractControllerTest
{
    protected $username = "userLog";
    protected $password = "userLog";

    /**
     * @param string $url
     * @param string $method
     *
     * @dataProvider provideApiUrl
     */
    public function testApi($url, $method = 'GET')
    {
        $this->client->request($method, $url);

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return array
     */
    public function provideApiUrl()
    {
        return array(
            array('/api/user/root'),
            array('/api/user'),
            array('/api/user/root/delete', 'DELETE'),

        );
    }
}
