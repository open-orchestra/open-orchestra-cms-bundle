<?php

namespace OpenOrchestra\BackofficeBundle\Tests\EventListener;

use Phake;
use OpenOrchestra\ModelBundle\Document\Site;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\BackofficeBundle\EventListener\NodeThemeSelectionListener;

/**
 * Class NodeThemeSelectionListenerTest
 */
class NodeThemeSelectionListenerTest extends AbstractBaseTestCase
{
    /**
     * @var NodeThemeSelectionListener
     */
    protected $listener;

    protected $siteRepository;
    protected $event;
    protected $object;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->object = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        $this->siteRepository = Phake::mock('OpenOrchestra\ModelBundle\Repository\SiteRepository');

        Phake::when($this->event)->getData()->thenReturn($this->object);

        $this->listener = new NodeThemeSelectionListener($this->siteRepository);
    }

    /**
     * Test if method is present
     */
    public function testCallable()
    {
        $this->assertTrue(is_callable(array($this->listener, 'preSetData')));
        $this->assertTrue(is_callable(array($this->listener, 'submit')));
    }

    /**
     * @param string $nodeTheme
     * @param Site   $site
     * @param int    $callTimes
     *
     * @dataProvider provideSiteAndTheme
     */
    public function testSubmit($nodeTheme, $site, $callTimes)
    {
        Phake::when($this->object)->getTheme()->thenReturn($nodeTheme);
        Phake::when($this->siteRepository)->findOneBySiteId(Phake::anyParameters())->thenReturn($site);

        $this->listener->submit($this->event);

        Phake::verify($this->object, Phake::times($callTimes))->setTheme("SiteFakeTheme");
        Phake::verify($this->object, Phake::times($callTimes))->setDefaultSiteTheme(true);
        Phake::verify($this->object, Phake::times(! $callTimes))->setDefaultSiteTheme(false);
    }

    /**
     * @return array
     */
    public function provideSiteAndTheme()
    {
        $site = Phake::mock('OpenOrchestra\ModelBundle\Document\Site');
        $theme = Phake::mock('OpenOrchestra\ModelBundle\Document\Theme');
        Phake::when($site)->getTheme()->thenReturn($theme);
        Phake::when($theme)->getName()->thenReturn("SiteFakeTheme");

        return array(
            array(NodeInterface::THEME_DEFAULT, $site, 1),
            array("SiteFakeTheme", $site, 0),
            array("FakeTheme", $site, 0),
        );
    }

    /**
     * @param bool $hasDefaultThemeSite
     * @param int  $callTimes
     *
     * @dataProvider provideHasDefaultThemeSite
     */
    public function testPreSetData($hasDefaultThemeSite, $callTimes)
    {
        Phake::when($this->object)->hasDefaultSiteTheme()->thenReturn($hasDefaultThemeSite);

        $this->listener->preSetData($this->event);

        Phake::verify($this->object, Phake::times($callTimes))->setTheme(NodeInterface::THEME_DEFAULT);
    }

    /**
     * @return array
     */
    public function provideHasDefaultThemeSite()
    {
        return array(
            array(true, 1),
            array(false, 0)
        );
    }
}
