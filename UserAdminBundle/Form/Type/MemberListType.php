<?php

namespace OpenOrchestra\UserAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MemberListType
 */
class MemberListType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('members_collection', 'collection', array(
            'type' => 'oo_member_element',
            'label' => 'open_orchestra_group.form.group.member',
            'data' => $options['data'],
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_member_list';
    }
}
