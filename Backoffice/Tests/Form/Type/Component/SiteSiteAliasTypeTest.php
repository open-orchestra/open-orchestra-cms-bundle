<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type\Component;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\Component\SiteSiteAliasType;

/**
 * Class SiteSiteAliasTypeTest
 */
class SiteSiteAliasTypeTest extends AbstractBaseTestCase
{
    /**
     * @var SiteSiteAliasType
     */
    protected $form;
    protected $idSite1 = 'fakeIdSite1';
    protected $nameSite1 = 'fakeNameSite1';
    protected $idSite2 = 'fakeIdSite2';
    protected $nameSite2 = 'fakeNameSite2';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        $siteSubscriber = Phake::mock('OpenOrchestra\Backoffice\EventSubscriber\SiteSubscriber');

        $site1 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site1)->getSiteId()->thenReturn($this->idSite1);
        Phake::when($site1)->getName()->thenReturn($this->nameSite1);
        $site2 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site2)->getSiteId()->thenReturn($this->idSite2);
        Phake::when($site2)->getName()->thenReturn($this->nameSite2);
        Phake::when($siteRepository)->findByDeleted(false)->thenReturn(array(
            $site1,
            $site2
        ));

        $this->form = new SiteSiteAliasType($siteRepository, $siteSubscriber);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->form);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('oo_site_site_alias', $this->form->getName());
    }

    /**
     * Test builder
     */
    public function testBuilder()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->addEventSubscriber(Phake::anyParameters())->thenReturn($builder);

        $this->form->buildForm($builder, array());

        Phake::verify($builder)->add('siteId', 'choice', array(
            'label' => false,
            'choices' => array(
                $this->idSite1 => $this->nameSite1,
                $this->idSite2 => $this->nameSite2,
            ),
            'attr' => array(
                'class' => 'to-tinyMce',
                'data-key' => 'site'
            ),
            'required' => false,
        ));
        Phake::verify($builder, Phake::times(1))->addEventSubscriber(Phake::anyParameters());
    }
}
