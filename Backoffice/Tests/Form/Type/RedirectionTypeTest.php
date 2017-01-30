<?php

namespace OpenOrchestra\UserBundle\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\RedirectionType;
use OpenOrchestra\Backoffice\Validator\Constraints\UniqueRedirection;

/**
 * Class RedirectionTypeTest
 */
class RedirectionTypeTest extends AbstractBaseTestCase
{
    /**
     * @var RedirectionType
     */
    protected $form;

    protected $redirectionClass = 'redirectionClass';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new RedirectionType($this->redirectionClass);
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
        $this->assertSame('oo_redirection', $this->form->getName());
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

        Phake::verify($builder, Phake::times(7))->add(Phake::anyParameters());
        Phake::verify($builder, Phake::times(1))->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'data_class' => $this->redirectionClass,
            'constraints'  => array(new UniqueRedirection()),
            'group_enabled' => true,
            'group_render' => array(
                'redirection' => array(
                    'rank'  => 0,
                    'label' => 'open_orchestra_backoffice.form.redirection.edit.title',
                ),
            ),
            'sub_group_render' => array(
                'properties' => array(
                    'rank'  => 0,
                    'label' => 'open_orchestra_backoffice.form.redirection.group.properties',
                ),
                'redirection' => array(
                    'rank'  => 10,
                    'label' => 'open_orchestra_backoffice.form.redirection.group.redirection',
                ),
            ),
        ));
    }

    /**
     * test buildView
     *
     * @param string      $data
     * @param string      $url
     * @param boolean     $mustSet
     * @param string|null $type
     *
     * @dataProvider provideData
     */
    public function testBuildView($data, $url, $mustSet, $type = null)
    {
        $formView = Phake::mock('Symfony\Component\Form\FormView');

        $formType = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($formType)->getData()->thenReturn($data);

        $formUrl = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($formUrl)->getData()->thenReturn($url);

        $form = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($form)->get('type')->thenReturn($formType);
        Phake::when($form)->get('url')->thenReturn($formUrl);

        $this->form->buildView($formView, $form, array());
        if ($mustSet) {
            Phake::verify($formType)->setData($type);
        } else {
            Phake::verify($formType, Phake::never())->setData(Phake::anyParameters());
        }
    }

    /**
     * provide data
     *
     * @return array
     */
    function provideData()
    {
        return array(
            'No data, no url' => array(null  , ''  , true, RedirectionType::TYPE_INTERNAL),
            'No data, url'  => array(null  , 'ok'  , true, RedirectionType::TYPE_EXTERNAL),
            'Ok' => array('data', 'ok', false),
        );
    }
}
