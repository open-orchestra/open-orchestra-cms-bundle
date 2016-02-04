<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Functional\Controller;

/**
 * Class FormControllersTest
 *
 * @group backofficeTest
 */
class FormControllersTest extends AbstractControllerTest
{
    /**
     * @param string $url
     *
     * @dataProvider provideApiUrl
     */
    public function testForm($url)
    {
        $this->client->request('GET', $url);

        $this->assertForm($this->client->getResponse());
    }

    /**
     * @return array
     */
    public function provideApiUrl()
    {
        return array(
            array('/admin/site/form/2'),
            array('/admin/status/new'),
            array('/admin/theme/new'),
            array('/admin/keyword/new'),
            array('/admin/template/form/template_full'),
            array('/admin/template/area/form/template_full/left_menu'),
            array('/admin/content-type/new'),
            array('/admin/role/new'),
            array('/admin/group/new'),
            array('/admin/redirection/new'),
            array('/admin/template_flex/new'),
        );
    }
}
