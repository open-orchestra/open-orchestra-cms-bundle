<?php

namespace OpenOrchestra\Backoffice\Form\Type\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


/**
 * Class FormTypeGroupExtension
 */
class FormTypeGroupExtension extends AbstractTypeExtension
{
    CONST DEFAULT_SUB_GROUP = '_default';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('group_enabled', $options['group_enabled']);
        $builder->setAttribute('group_label', $options['group_label']);
        $builder->setAttribute('group_rank', $options['group_rank']);
        $builder->setAttribute('sub_group', $options['sub_group']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if($form->getConfig()->getAttribute('group_enabled')) {
            $view->vars['group'] = array();
            $groupLabels = $form->getConfig()->getAttribute('group_label');
            foreach ($form->all() as $child) {
                $rank = $child->getConfig()->getAttribute('group_rank');
                $rank = array_key_exists($rank, $groupLabels) ? $groupLabels[$rank] : $rank;
                $subGroup = $child->getConfig()->getAttribute('sub_group');
                if (!array_key_exists($rank, $view->vars['group'])) {
                    $view->vars['group'][$rank] = array();
                }
                if (!array_key_exists($subGroup, $view->vars['group'][$rank])) {
                    $view->vars['group'][$rank][$subGroup] = array();
                }
                array_push($view->vars['group'][$rank][$subGroup], $child->getName());
            }
            $view->vars['group'] = array_merge(array_flip($form->getConfig()->getAttribute('group_label')), $view->vars['group']);
            $view->vars['group_enabled'] = $form->getConfig()->getAttribute('group_enabled');
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'group_enabled' => false,
            'group_label' => array(),
            'group_rank' => 0,
            'sub_group' => self::DEFAULT_SUB_GROUP,
        ));
    }

    public function getExtendedType()
    {
        return 'form';
    }
}
