<?php

namespace OpenOrchestra\UserAdminBundle\Tests\Form\Type;

use OpenOrchestra\UserAdminBundle\EventSubscriber\UserGroupsSubscriber;
use OpenOrchestra\UserAdminBundle\EventSubscriber\UserProfilSubscriber;
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

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $parameters = array(0 => 'en', 1 => 'fr');
        $userProfilSubscriber = Phake::mock(UserProfilSubscriber::class);
        $userGroupSubscriber = Phake::mock(UserGroupsSubscriber::class);

        $this->form = new UserType($this->class, $parameters, $userProfilSubscriber, $userGroupSubscriber);
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

        return array(
            'without_groups_edition' => array(array('edit_groups' => 'false', 'required_password' => false, 'self_editing' => false, 'data' => $site,), true, 5),
            'with_groups_edition' => array(array('edit_groups' => 'true', 'required_password' => false, 'self_editing' => true, 'data' => $site), false, 7)
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
