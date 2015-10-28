<?php

namespace OpenOrchestra\ApiBundle\Tests\Functional\Controller;

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
            array('/api/node/root'),
            array('/api/node/root/show-or-create'),
            array('/api/node/root/delete', "DELETE"),
            array('/api/node/root/duplicate/1', "POST"),
            array('/api/node/root/update', "POST"),
            array('/api/node/root/list-statuses'),
            array('/api/node/root/list-version'),
            array('/api/node/list/not-published-by-author'),
            array('/api/node/list/by-author'),
            array('/api/api-client'),
            array('/api/api-client/root/delete', "DELETE"),
            array('/api/content-type'),
            array('/api/content-type/fake-content-type-id'),
            array('/api/content-type/fake-content-type-id/delete', "DELETE"),
            array('/api/site/root'),
            array('/api/site'),
            array('/api/site/root/delete', "DELETE"),
            array('/api/keyword/check'),
            array('/api/keyword/fake_id'),
            array('/api/keyword/fake_id/delete', "DELETE"),
            array('/api/keyword'),
        );
    }
}
