<?php

namespace OpenOrchestra\ApiBundle\Tests\Functional\Controller;

use OpenOrchestra\ApiBundle\Tests\Functional\Controller\AbstractControllerTest;

/**
 * Class ApiControllersTest
 */
class ApiControllersTest extends AbstractControllerTest
{
    /**
     * @param string $url
     *
     * @dataProvider provideApiUrl
     */
    public function testApi($url)
    {
        $this->client->request('GET', $url);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $this->client->getResponse()->headers->get('content-type'));
    }

    /**
     * @return array
     */
    public function provideApiUrl()
    {
        return array(
            array('/api/node/root/show-or-create'),
            array('/api/node/root/show-or-create?language=en'),
            array('/api/node/transverse/show-or-create'),
            array('/api/node/fixture_full/show-or-create'),
            array('/api/node/fixture_full/show-or-create?language=en'),
            array('/api/content'),
            array('/api/content?content_type=news'),
            array('/api/content-type'),
            array('/api/site'),
            array('/api/theme'),
            array('/api/role'),
            array('/api/group'),
            array('/api/redirection'),
            array('/api/status'),
            array('/api/template/template_full'),
            array('/api/context/site/2/www.bOpenOrchestra.fr'),
        );
    }
}
