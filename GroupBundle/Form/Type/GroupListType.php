<?php

namespace OpenOrchestra\GroupBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\GroupBundle\Form\DataTransformer\GroupListToArrayTransformer;

/**
 * Class GroupListType
 */
class GroupListType extends AbstractType
{
    protected $groupListToArrayTransformer;

    /**
     * @param GroupListToArrayTransformer $groupListToArrayTransformer
     */
    public function __construct(
        GroupListToArrayTransformer $groupListToArrayTransformer
    ) {
        $this->groupListToArrayTransformer = $groupListToArrayTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->groupListToArrayTransformer);

        $builder->add('groups_collection', 'collection', array(
                'type' => 'oo_group_element',
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype_name' => '__id__',

        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_group_list';
    }
}
