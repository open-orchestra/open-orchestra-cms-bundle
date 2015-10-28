<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Functional\Controller;

/**
 * Class FormControllersSecurityTest
 */
class FormControllersSecurityTest extends AbstractControllerTest
{
    protected $username = 'userNoAccess';
    protected $password = 'userNoAccess';

    /**
     * @param string $url
     *
     * @dataProvider provideApiUrl
     */
    public function testForm($url)
    {
        $this->client->request('GET', $url);
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return array
     */
    public function provideApiUrl()
    {
        return array(
            array('/admin/node/form/root'),
            array('/admin/node/new/root'),
            array('/admin/form/root'),
            array('/admin/new'),
            array('/admin/content-type/form/fake-content-type-id'),
            array('/admin/content-type/form/new'),
            array('/admin/site/form/root'),
            array('/admin/site/new'),
            array('/admin/keyword/form/fake_keyword_id}'),
            array('/admin/keyword/new'),
            array('/admin/group/new'),
            array('/admin/group/form/groupID'),
            array('/admin/content/form/root'),
            array('/admin/content/new/root'),
        );
    }
}
