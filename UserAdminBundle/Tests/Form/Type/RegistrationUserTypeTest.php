<?php

namespace OpenOrchestra\UserAdminBundle\Tests\Form\Type;

use OpenOrchestra\UserAdminBundle\Form\Type\RegistrationUserType;
use Phake;

/**
 * Class RegistrationUserTypeTest
 */
class RegistrationUserTypeTest extends AbstractUserTypeTest
{
    /**
     * @var RegistrationUserType
     */
    protected $form;
    protected $class = 'OpenOrchestra\UserBundle\Document\User';

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $user = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($user)->getGroups()->thenReturn(array());
        $token = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        Phake::when($token)->getUser()->thenReturn($user);
        $tokenStorage = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        Phake::when($tokenStorage)->getToken()->thenReturn($token);
        $parameters = array(0 => 'en', 1 => 'fr');

        $this->form = new RegistrationUserType($objectManager, $tokenStorage, $this->class, $parameters);
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

        return array(
            'without_groups_edition' => array(array('edit_groups' => 'false', 'required_password' => true, 'self_editing' => false, 'data' => $site,), true, 6),
            'with_groups_edition' => array(array('edit_groups' => 'true',  'required_password' => true, 'self_editing' => true, 'data' => $site), false, 8)
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
            'group_enabled' => true,
            'required_password' => false,
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
