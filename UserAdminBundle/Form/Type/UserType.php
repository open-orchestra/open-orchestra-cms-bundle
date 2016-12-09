<?php

namespace OpenOrchestra\UserAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\UserAdminBundle\EventSubscriber\UserGroupsSubscriber;
use OpenOrchestra\UserAdminBundle\EventSubscriber\UserProfilSubscriber;

/**
 * Class UserType
 */
class UserType extends AbstractType
{
    protected $objectManager;
    protected $tokenStorage;
    protected $class;
    protected $availableLanguages;
    protected $user;

    /**
     * @param ObjectManager         $objectManager
     * @param TokenStorageInterface $tokenStorage
     * @param string                $class
     * @param array                 $availableLanguages
     */
    public function __construct(
        ObjectManager $objectManager,
        TokenStorageInterface $tokenStorage,
        $class,
        array $availableLanguages
    ) {
        $this->objectManager = $objectManager;
        $this->class = $class;
        $this->availableLanguages = $availableLanguages;
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $sitesId = array();
        $disabled = false;
        if (array_key_exists('data', $options) && ($user = $options['data']) instanceof UserInterface) {
            $sitesId = array_keys($user->getLanguageBySites());
            $disabled = !$user->isEditAllowed() && $options['self_editing'];
        }

        $builder
            ->add('firstName', 'text', array(
                'label' => 'open_orchestra_user_admin.form.user.firstName',
                'group_id' => 'information',
                'sub_group_id' => 'contact_information',
                'disabled' => $disabled,
            ))
            ->add('lastName', 'text', array(
                'label' => 'open_orchestra_user_admin.form.user.lastName',
                'group_id' => 'information',
                'sub_group_id' => 'contact_information',
                'disabled' => $disabled,
            ))
            ->add('email', 'email', array(
                'label' => 'open_orchestra_user_admin.form.user.email',
                'group_id' => 'information',
                'sub_group_id' => 'contact_information',
                'disabled' => $disabled,
            ));
        if ($options['self_editing']) {
            $builder
                ->add('current_password', 'password', array(
                    'label' => 'form.current_password',
                    'translation_domain' => 'FOSUserBundle',
                    'mapped' => false,
                    'group_id' => 'authentication',
                    'sub_group_id' => 'identifier',
                    'required' => false,
                ))
                ->add('plainPassword', 'repeated', array(
                    'type' => 'password',
                    'options' => array('translation_domain' => 'FOSUserBundle'),
                    'first_options' => array('label' => 'form.new_password'),
                    'second_options' => array('label' => 'form.new_password_confirmation'),
                    'invalid_message' => 'fos_user.password.mismatch',
                    'group_id' => 'authentication',
                    'sub_group_id' => 'identifier',
                    'required' => false,
                ))
                ->add('language', 'choice', array(
                    'choices' => $this->getLanguages(),
                    'label' => 'open_orchestra_user_admin.form.user.language',
                    'group_id' => 'preference',
                    'sub_group_id' => 'backoffice',
                ))
                ->add('languageBySites', 'oo_language_by_sites', array(
                    'label' => false,
                    'sites_id' => $sitesId,
                    'group_id' => 'preference',
                    'sub_group_id' => 'language',
                ));
        } else {
            $builder
                ->add('plainPassword', 'repeated', array(
                    'type' => 'password',
                    'options' => array('translation_domain' => 'FOSUserBundle'),
                    'first_options' => array('label' => 'form.password'),
                    'second_options' => array('label' => 'form.password_confirmation'),
                    'invalid_message' => 'fos_user.password.mismatch',
                    'group_id' => 'authentication',
                    'sub_group_id' => 'identifier',
                    'required' => false,
                ))
                ->add('editAllowed', 'radio', array(
                    'label' => 'open_orchestra_user_admin.form.user.edit_allowed',
                    'required' => false,
                    'group_id' => 'information',
                    'sub_group_id' => 'profil',
                ));
            $builder->addEventSubscriber(new UserProfilSubscriber($this->user, $this->objectManager));
            $builder->addEventSubscriber(new UserGroupsSubscriber());
        }

        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'edit_groups' => true,
            'self_editing' => false,
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
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_user';
    }

    /**
     * @return array
     */
    protected function getLanguages()
    {
        $languages = array();

        foreach($this->availableLanguages as $language) {
            $languages[$language] = $language;
        }

        return $languages;
    }
}
