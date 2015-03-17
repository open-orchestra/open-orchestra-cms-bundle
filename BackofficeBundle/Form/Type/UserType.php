<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\UserBundle\Form\Type\UserType as BaseUserType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class UserType
 */
class UserType extends BaseUserType
{
    protected $groupClass;

    /**
     * @param string              $class
     * @param TranslatorInterface $translator
     * @param string              $groupClass
     */
    public function __construct($class, TranslatorInterface $translator, $groupClass)
    {
        parent::__construct($class, $translator);
        $this->groupClass = $groupClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('groups', 'document', array(
            'class' => 'OpenOrchestra\BackofficeBundle\Document\Group',
            'property' => 'name',
            'multiple' => true,
            'expanded' => true,
            'required' => false,
        ));
    }
}
