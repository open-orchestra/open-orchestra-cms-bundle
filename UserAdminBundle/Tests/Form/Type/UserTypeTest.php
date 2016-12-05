<?php

namespace OpenOrchestra\UserAdminBundle\Tests\Form\Type;

use OpenOrchestra\UserAdminBundle\Form\Type\UserType;
use Phake;

/**
 * Class UserTypeTest
 */
class UserTypeTest extends AbstractUserTypeTest
{
    /**
     * @var UserType
     */
    protected $form;

    protected $class = 'OpenOrchestra\UserBundle\Document\User';
    protected $twig;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $this->twig = Phake::mock('Twig_Environment');
        $parameters = array(0 => 'en', 1 => 'fr');

        $this->form = new UserType($objectManager, $this->class, $parameters);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('oo_user', $this->form->getName());
    }

    /**
     * Test builder
     *
     * @param array   $options
     * @param boolean $expectSubscriber
     *
     * @dataProvider provideOptions
     */
    public function testBuilder(array $options, $expectSubscriber, $nbrAdd)
    {
        $this->form->buildForm($this->builder, $options);

        Phake::verify($this->builder, Phake::times($nbrAdd))->add(Phake::anyParameters());

        if ($expectSubscriber) {
            Phake::verify($this->builder, Phake::times(2))->addEventSubscriber(Phake::anyParameters());
        }
    }

    /**
     * Provide form type options
     *
     * @return array
     */
    public function provideOptions()
    {
        $site = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($site)->getLanguageBySites()->thenReturn(array('en' => 'fakeLanguage', 'fr' => 'fakeLanguage'));

        $user = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($user)->getGroups()->thenReturn(array());

        return array(
            'without_groups_edition' => array(array('edit_groups' => 'false', 'self_editing' => false, 'data' => $site, 'current_user' => $user), true, 5),
            'with_groups_edition' => array(array('edit_groups' => 'true', 'self_editing' => true, 'data' => $site), false, 7)
        );
    }

    /**
     * Test configureOptions
     */
    public function testResolver()
    {
        $this->form->configureOptions($this->resolver);
        Phake::verify($this->resolver)->setDefaults(array(
            'data_class' => $this->class,
            'edit_groups' => true,
            'self_editing' => false,
            'current_user' => null,
            'allowed_sites' => array(),
            'group_enabled' => true,
            'group_render' => array(
                'information' => array(
                    'rank' => 0,
                    'label' => 'open_orchestra_user_admin.form.user.group.information',
                ),
                'authentication' => array(
                    'rank' => 1,
                    'label' => 'open_orchestra_user_admin.form.user.group.authentication',
                ),
                'preference' => array(
                    'rank' => 2,
                    'label' => 'open_orchestra_user_admin.form.user.group.preference',
                ),
            ),
            'sub_group_render' => array(
                'contact_information' => array(
                    'rank' => 0,
                    'label' => 'open_orchestra_user_admin.form.user.sub_group.contact_information',
                ),
                'group' => array(
                    'rank' => 1,
                    'label' => 'open_orchestra_user_admin.form.user.sub_group.group',
                ),
                'profil' => array(
                    'rank' => 2,
                    'label' => 'open_orchestra_user_admin.form.user.sub_group.profil',
                ),
                'identifier' => array(
                    'rank' => 0,
                    'label' => 'open_orchestra_user_admin.form.user.sub_group.identifier',
                ),
                'backoffice' => array(
                    'rank' => 0,
                    'label' => 'open_orchestra_user_admin.form.user.sub_group.backoffice',
                ),
                'language' => array(
                    'rank' => 1,
                    'label' => 'open_orchestra_user_admin.form.user.sub_group.language',
                ),
            ),
        ));
        Phake::verify($this->resolver)->setDefaults(Phake::anyParameters());
    }
}
