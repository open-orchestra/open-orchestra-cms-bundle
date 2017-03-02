<?php

namespace OpenOrchestra\GroupBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class GroupRoleType
 */
class GroupRoleType extends AbstractType
{
    protected $translator;
    protected $groupRolesConfiguration;

    /**
     * @param TranslatorInterface $translator
     * @param array               $groupRolesConfiguration
     */
    public function __construct(
        TranslatorInterface $translator,
        array $groupRolesConfiguration
    ) {
            $this->groupRolesConfiguration = $groupRolesConfiguration;
            $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $configuration = array();
        foreach ($this->groupRolesConfiguration as $tableName => $chekLists) {
            $chekList = reset($chekLists);
            foreach ($chekList as $columnConfiguration) {
                $configuration[$tableName]['row'][] = $this->translator->trans($columnConfiguration['label']) ;
                if (array_key_exists('help_text', $columnConfiguration)) {
                    $configuration[$tableName]['help'][] = $this->translator->trans($columnConfiguration['help_text']) ;
                }
            }
            foreach ($chekLists as $chekListName => $chekList) {
                $configuration[$tableName]['column'][$chekListName] = $this->translator->trans('open_orchestra_backoffice.form.role.' . $chekListName);
            }
        }

        $builder
            ->add('roles_collections', 'collection', array(
                'entry_type' => 'oo_check_list_collection',
                'label' => false,
                'entry_options' => array(
                    'configuration' => $configuration
         )));
   }

   /**
    * @param FormView      $view
    * @param FormInterface $form
    * @param array         $options
    */
   public function buildView(FormView $view, FormInterface $form, array $options)
   {
       $view->vars['configuration'] = array_keys($this->groupRolesConfiguration);
   }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_group_role';
    }
}
