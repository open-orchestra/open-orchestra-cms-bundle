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
    CONST DEFAULT_SUB_GROUP = '_default_sub_group';
    CONST DEFAULT_GROUP = '_default_group';

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('group_enabled', $options['group_enabled']);
        $builder->setAttribute('group_id', $options['group_id']);
        $builder->setAttribute('sub_group_id', $options['sub_group_id']);
        $builder->setAttribute('group_render', $options['group_render']);
        $builder->setAttribute('sub_group_render', $options['sub_group_render']);
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($form->getConfig()->getAttribute('group_enabled')) {
            $view->vars['group'] = array();
            $groupRender = $form->getConfig()->getAttribute('group_render');
            $subGroupRender = $form->getConfig()->getAttribute('sub_group_render');
            foreach ($form->all() as $child) {
                list($groupKey, $groupLabel) = $this->generateKeyLabel($child, 'group_id', self::DEFAULT_GROUP, $groupRender);
                list($subGroupKey, $subGroupLabel) = $this->generateKeyLabel($child, 'sub_group_id', self::DEFAULT_SUB_GROUP, $subGroupRender);
                if (!array_key_exists($groupKey, $view->vars['group'])) {
                    $view->vars['group'][$groupKey] = array();
                }
                if (!array_key_exists($subGroupKey, $view->vars['group'][$groupKey])) {
                    $view->vars['group'][$groupKey][$subGroupKey] = array(
                        'children' => array(),
                        'group_label' => $groupLabel,
                        'sub_group_label' => $subGroupLabel,
                    );
                }
                array_push($view->vars['group'][$groupKey][$subGroupKey]['children'], $child->getName());
            }
            $view->vars['group'] = $this->ksort_recursive($view->vars['group']);
            $view->vars['group_enabled'] = $form->getConfig()->getAttribute('group_enabled');
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'group_enabled' => false,
            'group_id' => self::DEFAULT_GROUP,
            'sub_group_id' => self::DEFAULT_SUB_GROUP,
            'group_render' => array(),
            'sub_group_render' => array(),
        ));
    }

    /**
     * Returns the name of extended type.
     *
     * @return string The name of extended type
     */
    public function getExtendedType()
    {
        return 'form';
    }

    /**
     * @param FormInterface $form
     * @param string        $id
     * @param string        $default
     * @param string        $reference
     *
     * @return array
     */
    protected function generateKeyLabel(FormInterface $form, $id, $default, $reference)
    {
        $key = $default;
        $label = $default;
        if ($form->getConfig()->hasAttribute($id)) {
            $id = $form->getConfig()->getAttribute($id);
            if (array_key_exists($id, $reference)) {
                $key = array_key_exists('rank', $reference[$id]) ? $reference[$id]['rank'] : $id;
                $label = array_key_exists('label', $reference[$id]) ? $reference[$id]['label'] : $id;
            }
        }

        return array($key, $label);
    }

    /**
     * @param array $array
     *
     * @return array
     */
    protected function ksort_recursive(&$array)
    {
       foreach ($array as &$value) {
           if (is_array($value)) {
               $this->ksort_recursive($value);
           }
       }
       ksort($array, SORT_NATURAL);

       return $array;
    }
}
