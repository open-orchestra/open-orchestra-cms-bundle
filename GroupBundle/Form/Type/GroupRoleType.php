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
        $maxColumns = 0;

        foreach ($this->groupRolesConfiguration as $fieldset => $tables) {
            foreach ($tables as $tableName => $chekLists) {
                if (!empty($chekLists)) {
                    $chekList = reset($chekLists);
                    foreach ($chekList as $columnConfiguration) {
                        $configuration[$tableName]['row'][] = $this->translator->trans($columnConfiguration['label']);
                        if (array_key_exists('icon', $columnConfiguration)) {
                            $configuration[$tableName]['icon'][] = $columnConfiguration['icon'];
                        }
                        if (array_key_exists('help', $columnConfiguration)) {
                            $configuration[$tableName]['help'][] = $this->translator->trans($columnConfiguration['help']);
                        }
                    }
                    foreach ($chekLists as $chekListName => $chekList) {
                        $configuration[$tableName]['column'][$chekListName] = $this->translator->trans('open_orchestra_backoffice.form.role.' . $chekListName);
                        $maxColumns = max($maxColumns, count($configuration[$tableName]['column']));
                    }
                }
            }
        }

        $builder
            ->add('roles_collections', 'collection', array(
                'entry_type' => 'oo_check_list_collection',
                'label' => false,
                'entry_options' => array(
                    'configuration' => $configuration,
                    'max_columns' => $maxColumns,
                )));
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        foreach ($this->groupRolesConfiguration as $fieldset => $tableConfiguration) {
            foreach ($tableConfiguration as $table => $configuration) {
                if (!empty($configuration)) {
                    $view->vars['configuration'][$fieldset][] = $table;
                }
            }
        }
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
