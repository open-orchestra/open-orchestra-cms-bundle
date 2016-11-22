<?php

namespace OpenOrchestra\UserAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\UserAdminBundle\EventSubscriber\UserGroupsSubscriber;

/**
 * Class UserType
 */
class UserType extends AbstractType
{
    protected $class;
    protected $availableLanguages;

    /**
     * @param string $class
     * @param array  $availableLanguages
     */
    public function __construct($class, array $availableLanguages)
    {
        $this->class = $class;
        $this->availableLanguages = $availableLanguages;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array(
                'label' => 'open_orchestra_user_admin.form.user.firstName',
                'group_id' => 'information',
                'sub_group_id' => 'contact_information',
                'disabled' => $options['self_editing'],
            ))
            ->add('lastName', 'text', array(
                'label' => 'open_orchestra_user_admin.form.user.lastName',
                'group_id' => 'information',
                'sub_group_id' => 'contact_information',
                'disabled' => $options['self_editing'],
            ))
            ->add('email', 'email', array(
                'label' => 'open_orchestra_user_admin.form.user.email',
                'group_id' => 'information',
                'sub_group_id' => 'contact_information',
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
                ));
        }
        $builder
            ->add('language', 'choice', array(
                'choices' => $this->getLanguages(),
                'label' => 'open_orchestra_user_admin.form.user.language',
                'group_id' => 'preference',
                'sub_group_id' => 'backoffice',
            ))
            ->add('languageBySites', 'oo_language_by_sites', array(
                'label' => false,
                'sites_id' => array_keys($options['data']->getLanguageBySites()),
                'group_id' => 'preference',
                'sub_group_id' => 'language',
            ));

        if ($options['edit_groups']) {
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
