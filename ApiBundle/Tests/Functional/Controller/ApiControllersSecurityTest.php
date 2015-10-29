<?php

namespace OpenOrchestra\ApiBundle\Tests\Functional\Controller;

/**
 * Class ApiControllersSecurityTest
 */
class ApiControllersSecurityTest extends AbstractControllerTest
{
    protected $username = "userNoAccess";
    protected $password = "userNoAccess";

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
            array('/api/trashcan/list'),
            array('/api/trashcan/fake_id/restore','PUT'),
            array('/api/group/groupID/delete', "DELETE"),
            array('/api/content'),
            array('/api/content/root'),
            array('/api/content/root/show-or-create'),
            array('/api/content/root/delete', 'DELETE'),
            array('/api/content/root/duplicate', "POST"),
            array('/api/content/root/update', 'POST'),
            array('/api/content/root/list-statuses'),
            array('/api/content/root/list-version'),
            array('/api/content/list/not-published-by-author'),
            array('/api/content/list/by-author'),
            array('/api/redirection'),
            array('/api/redirection/fake-id'),
            array('/api/redirection/fake-id/delete', "DELETE"),
            array('/api/status'),
            array('/api/status/root', 'DELETE'),
        );
    }
}
